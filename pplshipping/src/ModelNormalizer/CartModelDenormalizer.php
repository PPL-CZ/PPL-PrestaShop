<?php
namespace PPLShipping\ModelNormalizer;

use PPLShipping\Model\Model\CategoryRulesModel;
use PPLShipping\Model\Model\CountryModel;
use PPLShipping\Model\Model\CartModel;

use PPLShipping\Model\Model\ParcelPlacesModel;
use PPLShipping\Model\Model\ProductRulesModel;
use PPLShipping\Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

class CartModelDenormalizer implements DenormalizerInterface
{

    public function denormalize($data, string $type, ?string $format = null, array $context = [])
    {
        /**
         * @var \Cart $data
         */
        $shipmentCartModel = new CartModel();

        $shipmentCartModel->setParcelRequired(false);
        $shipmentCartModel->setMapEnabled(false);
        $shipmentCartModel->setAgeRequired(false);
        $shipmentCartModel->setDisableCod(true);
        $shipmentCartModel->setDisabledByProduct(false);
        $shipmentCartModel->setDisabledByCountry(false);
        $shipmentCartModel->setDisabledByRules(false);
        $shipmentCartModel->setParcelShopEnabled(true);
        $shipmentCartModel->setParcelBoxEnabled(true);
        $shipmentCartModel->setAlzaBoxEnabled(true);

        if (!isset($context['carrier']))
            $carrier = new \Carrier($data->id_carrier);
        else
            $carrier = $context['carrier'];

        $code = \Configuration::getGlobalValue("PPLCarrier{$carrier->id_reference}");

        if ($code) {
           $shipmentCartModel->setParcelRequired(pplcz_parcel_required($code));
           $shipmentCartModel->setMapEnabled(pplcz_parcel_required($code));
        }




        $codCountries = pplcz_get_cod_currencies();
        $addressId = $data->id_address_delivery;
        $address = new \Address($addressId);
        $idCountry = $address->id_country;
        $countryCode = null;
        if ($idCountry)
        {
            $country = new \Country($idCountry);
            $countryCode = $country->iso_code;
        }


        $zones = $carrier->getZones();
        $iso_codes = [];
        foreach ($zones as $zone) {
            $zone_countries = \Country::getCountriesByZoneId($zone['id_zone'], (int)\Configuration::get('PS_LANG_DEFAULT'));
            foreach ($zone_countries as $country) {
                if (!in_array($country['iso_code'], $iso_codes)) {
                    $iso_codes[] = $country['iso_code'];
                }
            }
        }
        $pplcountries = array_keys(pplcz_get_allowed_countries());
        $iso_codes = array_intersect($pplcountries, $iso_codes);

        if (!in_array($countryCode, $iso_codes, true)) {
            $shipmentCartModel->setDisabledByCountry(true);
        }

        if (in_array($code, ["SMAR", "SMEU"], true) && !in_array($countryCode, ["DE", "CZ", "SK", "PL"]))
            $shipmentCartModel->setDisabledByCountry(true);

        $max = 100000;
        static $inNormalizer;
        if (!$inNormalizer) {
            $inNormalizer = true;
            $max = $data->getOrderTotal(true, \Cart::BOTH_WITHOUT_SHIPPING );
            $inNormalizer = false;
        }

        $currency = new \Currency($data->id_currency);

        $countryAndBankAccount = pplcz_get_cod_currencies();
        $accountIn = array_filter($countryAndBankAccount, function($item) use ($countryCode, $currency) {
            return $item['country'] ==  $countryCode && $item['currency'] == $currency->iso_code;
        });
        $limits = include __DIR__ . '/../config/limits.php';
        $codName = pplcz_get_cod_name($code);

        $maxCodPrice =  array_values(array_filter($limits['COD'], function ($item) use ($currency, $codName) {
            if ($item['product'] === $codName && $item['currency'] === $currency->iso_code) {
                return true;
            }
            return false;
        }, true));

        if ($shipmentCartModel->getParcelRequired()) {
            /**
             * @var ParcelPlacesModel $parcelplaces
             */
            $parcelplaces = pplcz_denormalize(new \Configuration(), ParcelPlacesModel::class);

            $hasCountries = array_diff(["CZ", "DE", "PL", "SK"], $parcelplaces->getDisabledCountries() ?: []);
            $disabledByCountry = !in_array($countryCode, $hasCountries, true);

            $hasCountries = array_intersect(array_values($hasCountries), array_values($iso_codes));

            $shipmentCartModel->setEnabledParcelCountries($hasCountries);

            if (!in_array($countryCode, $hasCountries, true)) {
                $shipmentCartModel->setDisabledByCountry($disabledByCountry);
                $shipmentCartModel->setParcelShopEnabled(false);
                $shipmentCartModel->setParcelBoxEnabled(false);
                $shipmentCartModel->setParcelShopEnabled(false);
            }

            if ($parcelplaces->getDisabledParcelShop())
                $shipmentCartModel->setParcelShopEnabled(false);
            if ($parcelplaces->getDisabledParcelBox())
                $shipmentCartModel->setParcelBoxEnabled(false);
            if ($parcelplaces->getDisabledAlzaBox())
                $shipmentCartModel->setAlzaBoxEnabled(false);
        }



        if ($accountIn && $maxCodPrice && $maxCodPrice[0]['max'] > $max)
        {
            $shipmentCartModel->setDisableCod(false);
        }

        foreach ($data->getProducts() as $product) {
            $id_product = $product['id_product'];
            /**
             * @var ProductRulesModel $rules
             */
            $rules = pplcz_denormalize(\PPLBaseDisabledRule::getByProduct($id_product), ProductRulesModel::class);
            $this->applyRules($shipmentCartModel, $code, $rules );

            $ids = \Product::getProductCategories($id_product);
            $usedCats = [];
            while ($ids)
            {
                $item = array_pop($ids);
                if (in_array((int)$item, $usedCats, true))
                    continue;

                $usedCats[] = (int)$item;
                /**
                 * @var CategoryRulesModel $rules
                 */
                $rules = pplcz_denormalize(\PPLBaseDisabledRule::getByCagetory($item), CategoryRulesModel::class);
                $this->applyRules($shipmentCartModel, $code, $rules);
                $cat = new \Category($item);
                if ($cat && $cat->id_parent)
                    $ids[] =  (int)$cat->id_parent;
            }
        }


        if (!$shipmentCartModel->getParcelShopEnabled() && !$shipmentCartModel->getParcelBoxEnabled() && !$shipmentCartModel->getAlzaBoxEnabled() && $shipmentCartModel->getParcelRequired())
        {
            $shipmentCartModel->setDisabledByRules(true);
        }

        if (!$shipmentCartModel->getParcelShopEnabled() && $shipmentCartModel->getAgeRequired() && $countryCode === 'CZ')
        {
            $shipmentCartModel->setDisabledByRules(true);
        }

        return $shipmentCartModel;
    }

    /**
     * @param CategoryRulesModel|ProductRulesModel $rules
     * @return void
     */
    private function applyRules(CartModel $shipmentCartModel, $code, $rules)
    {
        if ($rules) {
            $shipmentCartModel->setAlzaBoxEnabled(!$rules->getPplDisabledAlzaBox() && $shipmentCartModel->getAlzaBoxEnabled());
            $shipmentCartModel->setParcelBoxEnabled(!$rules->getPplDisabledParcelBox() && $shipmentCartModel->getParcelBoxEnabled());
            $shipmentCartModel->setParcelShopEnabled(!$rules->getPplDisabledParcelShop() && $shipmentCartModel->getParcelShopEnabled());
            $shipmentCartModel->setAgeRequired(($rules->getPplConfirmAge15() || $rules->getPplConfirmAge18()) || $shipmentCartModel->getAgeRequired());

            $codeCod = pplcz_get_cod_name($code);
            if ($rules->getPplDisabledTransport() && in_array($codeCod, $rules->getPplDisabledTransport(), true))
                $shipmentCartModel->setDisableCod(true);
            if ($rules->getPplDisabledTransport() && in_array($code, $rules->getPplDisabledTransport(), true))
                $shipmentCartModel->setDisabledByProduct(true);

            if ($shipmentCartModel->getAgeRequired() && $code === "SMAR")
            {
                $shipmentCartModel->setAlzaBoxEnabled(false);
                $shipmentCartModel->setParcelBoxEnabled(false);
            }
        }
    }

    public function supportsDenormalization($data, string $type, ?string $format = null)
    {
        return $data instanceof \Cart && $type === CartModel::class;
    }
}