<?php
namespace PPLShipping\Validator;

use PPLShipping\Model\Model\CartModel;
use PPLShipping\Serializer;

class CartValidator extends ModelValidator
{

    public function canValidate($model)
    {
        if ($model instanceof \Cart)
            return true;
        return false;
    }

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

        if ($data->getDisabledByCountry() || $data->getDisabledByProduct() || $data->getDisabledByRules())
            $errors->add("shipment-error", "Špatná volba dopravy, zvolte jiný způsob");


        $parcel = \PPLParcel::getParcelByCartId($model->id);

        if ($data->getParcelRequired()) {
            if (!$parcel)
                $errors->add("parcelshop-missing", "Je potřeba vybrat výdejní místo pro doručení zásilky");
            else
            {
                if ($parcel->type === "ParcelShop" && !$data->getParcelShopEnabled())
                    $errors->add("parcelshop-parcel-disabled", "Parcelshop neni povolen");
                if ($parcel->type === "ParcelBox" && !$data->getParcelBoxEnabled())
                    $errors->add("parcelshop-parcel-disabled", "ParcelBox neni povolen");
                if ($parcel->type === "AlzaBox" && !$data->getParcelShopEnabled())
                    $errors->add("parcelshop-parcel-disabled", "AlzaBox neni povolen");
            }
        }

        if ($parcel && static::ageRequired($model, $code)
            && $parcel->type !== 'ParcelShop')
        {
            $errors->add("parcelshop-age-required", "Z důvodu kontroly věku je nutné vybrat obchod, ne výdejní box.");
        }

        $delivery = new \Address($model->id_address_delivery);
        $inv = new \Address($model->id_address_invoice);

        if (!$delivery->phone && !$delivery->phone_mobile && !$inv->phone && !$inv->phone_mobile) {
            $errors->add("parcelshop-phone-required", "Pro zasílání informací o stavu zásilky je nutno vyplnit telefonní číslo.");
        }

        return $errors;
    }

    public static function ageRequired(\Cart $cart, $shippingMethod) {
        return;
        if (in_array($shippingMethod, ["SMAR", "SMAD"], true)) {

            foreach ($cart->get_cart() as $key => $val) {
                $product = $val['product_id'];
                $variation = $val['variation'];
                if (array_reduce([$product, $variation], function ($carry, $item) {
                    if ($carry || !$item)
                        return $carry;

                    $variation = new \WC_Product($item);
                    /**
                     * @var ProductModel $model
                     */
                    $model = Serializer::getInstance()->denormalize($variation, ProductModel::class);
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