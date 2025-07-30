<?php
namespace PPLShipping\tmodule;


use PPLShipping\Model\Model\ParcelAddressModel;
use PPLShipping\Model\Model\CartModel;
use PPLShipping\Model\Model\ShipmentMethodModel;
use PPLShipping\Serializer;
use PPLShipping\Symfony\Component\Validator\Constraints\Country;

trait TDisplayExtraContent {

    public function hookDisplayHeader($params)
    {
        $assetGeneratedName = "-1.0.4";
        $this->context->controller->registerStylesheet(
            "ppl-label-method-css", "modules/pplshipping/assets/css/label-method{$assetGeneratedName}.css", [ "media" => "all"]
        );
        $this->context->controller->registerStylesheet(
            "ppl-map-css", "modules/pplshipping/assets/css/ppl-map{$assetGeneratedName}.css", [ "media" => "all"]
        );
        $this->context->controller->registerJavascript(
            "ppl-parcel-shop-js", "modules/pplshipping/assets/js/select-parcelshop{$assetGeneratedName}.js", [ "media" => "all"]
        );
        $this->context->controller->registerJavascript(
            "ppl-label-method-js", "modules/pplshipping/assets/js/ppl-map{$assetGeneratedName}.js", [ "media" => "all"]
        );


        $url = $this->context->link->getModuleLink('pplshipping', 'FrontMapPPL');
        \Media::addJsDef([
            "FrontMapPPLController"=> $url
        ]);
    }

    public function hookActionPresentPaymentOptions($params)
    {
        /**
         * @var CartModel $cartModel
         */
        $cartModel = pplcz_denormalize($params['cart'], CartModel::class);
        if ($cartModel->getDisableCod())
        {
            unset($params['paymentOptions']['ps_cashondelivery']);
        }
        return;
    }




    public function hookDisplayCarrierExtraContent($params)
    {

        /**
         * @var \pplshipping $this
         */
        $code = \Configuration::getGlobalValue("PPLCarrier{$params['carrier']['id_reference']}");
        if ($code) {
            /**
             * @var ShipmentMethodModel $method
             */
            if( pplcz_parcel_required($code)) {
                /**
                 * @var \Cart $cart
                 */
                $cart = $params['cart'];
                $address = new \Address($cart->id_address_delivery);
                $country = new \Country($address->id_country);
                $deliveryAddress = join(', ', [trim($address->address1 . ' ' . $address->address2), $address->city, $address->postcode]);

                /**
                 * @var ParcelAddressModel $parcel
                 */
                $parcel = pplcz_denormalize($cart, ParcelAddressModel::class);
                /**
                 * @var CartModel $cartModel
                 */
                $cartModel = pplcz_denormalize($cart, CartModel::class, ["carrier" =>  \Carrier::getCarrierByReference($params['carrier']['id_reference'])]);

                if ($parcel) {
                    $deliveryAddress = join(", ", [$parcel->getStreet(), $parcel->getCity()]);
                    $country = $parcel->getCountry();
                } else {
                    $country = $country->iso_code;
                }

                $hiddenPoints = [];

                if (!$cartModel->getAlzaBoxEnabled())
                    $hiddenPoints[] = "AlzaBox";
                if (!$cartModel->getParcelBoxEnabled())
                    $hiddenPoints[] = "ParcelBox";
                if (!$cartModel->getParcelShopEnabled())
                    $hiddenPoints[] = "ParcelShop";


                $cart = [
                    "parcel"=> $parcel,
                    "deliveryAddress"=> $deliveryAddress,
                    "hiddenPoints" => join(',', array_unique($hiddenPoints)),
                    "country" => strtolower($country),
                    "countries" => strtolower(join(",", $cartModel->getEnabledParcelCountries() ?: [])),
                    "image" => pplcz_asset_icon("ps_pb.png"),
                    'cartModel'=> $cartModel
                ];

                $this->context->smarty->assign($cart);

                return $this->display( $this->getReflFile(), './views/templates/hook/parcel.tpl');
            }
        }
    }
}