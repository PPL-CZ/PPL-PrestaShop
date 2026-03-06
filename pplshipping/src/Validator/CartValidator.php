<?php
namespace PPLShipping\Validator;

use PPLShipping\CPLOperation;
use PluginPpl\MyApi2\ApiException;
use PPLShipping\Model\Model\CartModel;
use PPLShipping\Serializer;
use PPLShipping\Setting\MethodSetting;

class CartValidator extends ModelValidator
{

    public function canValidate($model)
    {
        if ($model instanceof \Cart)
            return true;
        return false;
    }

    private static $accessPoints = [];

    public function validate($model, $errors, $path)
    {
        /**
         * @var \Cart $model
         */
        $carrier = new \Carrier($model->id_carrier);
        $code = \Configuration::getGlobalValue("PPLCarrier{$carrier->id_reference}");

        if (!$code)
            return;

        /**
         * @var CartModel $data
         */
        $data = Serializer::getInstance()->denormalize($model, CartModel::class);

        if ($data->getDisabledByCountry() || $data->getDisabledByProduct() || $data->getDisabledByRules() || $data->getDisabledBySize() || $data->getDisabledByWeight())
            $errors->add("shipment-error", "Špatná volba dopravy, zvolte jiný způsob");


        $parcel = \PPLParcel::getParcelByCartId($model->id);

        if ($parcel) {

            if (!isset(self::$accessPoints[$parcel->code])) {
                self::$accessPoints[$parcel->code] = true;
                try {
                    $cpl = new CPLOperation();
                    $accessToken = $cpl->getAccessToken();
                    if ($accessToken) {
                        $testparcel = $cpl->findParcel($parcel->code, $parcel->country, 10);
                        if (!$testparcel)
                            self::$accessPoints[$parcel->code] = false;
                    }
                } catch (\Exception $ex2) {
                    if ($ex2 instanceof ApiException && $ex2->getCode() === 404) {
                        self::$accessPoints[$parcel->code] = false;
                    }
                }
            }

            if (!self::$accessPoints[$parcel->code])
            {
                $errors->add("parcelshop-disabled-shop", "Vybrané výdejní místo se nepodařilo najít", "ppl-cz");
                return $errors;
            }

            switch ($parcel->type) {
                case 'ParcelShop':
                    if (!$data->getParcelShopEnabled())
                        $errors->add("parcelshop-disabled-shop", "V košíku je produkt, který neumožňuje vybrat obchod pro vyzvednutí zásilky");
                    break;
                case 'ParcelBox':
                    if (!$data->getParcelBoxEnabled())
                        $errors->add("parcelshop-disabled-box", "V košíku je produkt, který neumožňuje vybrat ParcelBox pro vyzvednutí zásilky");
                    break;
                case 'AlzaBox':
                    if (!$data->getAlzaBoxEnabled())
                        $errors->add("parcelshop-disabled-box", "V košíku je produkt, který neumožňuje vybrat AlzaBox pro vyzvednutí zásilky");
                    break;
                default:
                    $errors->add("parcelshop-disabled-box", "V košíku je produkt, který neumožňuje vybrat box pro vyzvednutí zásilky");
            }

            $enabledCountries = $data->getEnabledParcelCountries();
            if ($enabledCountries && !in_array($parcel->country, $enabledCountries, true))
            {
                $errors->add("parcelshop-disabled-country", "Nepovolená země výdejního místa");
            }
        }

        if ($data->getParcelRequired() && !$parcel) {
            $errors->add("parcelshop-missing", "Je potřeba vybrat výdejní místo pro doručení zásilky");
        }

        if (static::ageRequired($model, $code)
            && $parcel && $parcel->type !== 'ParcelShop')
        {
            $errors->add("parcelshop-age-required", "Z důvodu kontroly věku je nutné vybrat obchod, ne výdejní box.");
        }

        $delivery = new \Address($model->id_address_delivery);
        $inv = new \Address($model->id_address_invoice);

        $phone = $delivery->phone ?: $delivery->phone_mobile ?: $inv->phone ?: $inv->phone_mobile;

        if (!$phone) {
            $errors->add("parcelshop-phone-required", "Pro zasílání informací o stavu zásilky je nutno vyplnit telefonní číslo.");
        } else if (!self::isPhone($phone)) {
            $errors->add("parcelshop-phone-required", "Nevalidní telefonní číslo");
        }

        $deliveryCountry = \Country::getIsoById($delivery->id_country);
        if ($deliveryCountry && !self::isZip($deliveryCountry, $delivery->postcode)) {
            $errors->add("parcelshop-shippingzip-required", "Nevalidní PSČ u doručovací adresy");
        }

        if ($data->getParcelRequired() && $parcel && $deliveryCountry)
        {
            if ($deliveryCountry !== $parcel->country)
            {
                $errors->add("parcelshop-shippingzip-required", "Země kontaktní (doručovací) adresy je jiná, než výdejního místa");
            }
        }

        return $errors;
    }

    public static function ageRequired(\Cart $cart, $shippingMethod)
    {
        $delivery = new \Address($cart->id_address_delivery);
        $country = \Country::getIsoById($delivery->id_country);

        $methodid = MethodSetting::getMethodForCountry($country, $shippingMethod);

        if (in_array($methodid, ["SMAR", "SMAD"], true)) {
            foreach ($cart->getProducts() as $val) {
                $product = $val['id_product'];
                $variation = $val['id_product_attribute'];
                if (array_reduce([$product, $variation], function ($carry, $item) {
                    if ($carry || !$item)
                        return $carry;

                    $rule = \PPLBaseDisabledRule::getByProduct($item);
                    if (!$rule)
                        return $carry;

                    $model = pplcz_denormalize($rule, \PPLShipping\Model\Model\ProductRulesModel::class);
                    if ($model->getPplConfirmAge18()
                        || $model->getPplConfirmAge15()) {
                        $carry = true;
                    }
                    return $carry;
                }, false)) {
                    return true;
                }
            }
        }
        return false;
    }
}
