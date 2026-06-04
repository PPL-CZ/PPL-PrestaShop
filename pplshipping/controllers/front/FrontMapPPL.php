<?php

class pplshippingFrontMapPPLModuleFrontController extends ModuleFrontController
{
    public $ajax = true;

    public function initContent()
    {
        parent::initContent();
        header("Content-Type: text/html");

        $assetGeneratedName = "-1.1.0";

        $args = pplcz_get_new_map_args();
        $this->context->smarty->assign([
            'apikey' => $args['apikey'],
            'config_json' => json_encode($args['config']),
            "map_css" => Tools::getShopDomainSsl(true, true).__PS_BASE_URI__."modules/pplshipping/assets/css/ppl-external{$assetGeneratedName}.css",
            "map_js" => Tools::getShopDomainSsl(true, true).__PS_BASE_URI__."modules/pplshipping/assets/js/ppl-external{$assetGeneratedName}.js",
        ]);
        $this->setTemplate("module:pplshipping/views/templates/front/map-new.tpl");
    }

    public function displayAjaxSetParcel()
    {
        $cart = Context::getContext()->cart;
        $cartId = $cart->id;

        $deliver_options = $cart->getDeliveryOption();

        $id_delivery = trim(reset($deliver_options), ',');
        /**
         * @var \PPLShipping\Model\Model\CartModel $cart
         */
        $cart = \PPLShipping\Serializer::getInstance()->denormalize($cart, \PPLShipping\Model\Model\CartModel::class, null, ["carrier" => new Carrier($id_delivery?:$cart->id_carrier)]);
        if (!$cart->getParcelRequired())
        {
            http_response_code(400);
            die();
        }

        $inputJSON = file_get_contents('php://input');
        $input = @json_decode($inputJSON, true);
        $data = null;

        if ($input) {
            $input['accessPointCode'] = $input['code'];
            $input = json_encode($input);
            $data = \PluginPpl\MyApi2\ObjectSerializerPpl::deserialize($input, \PluginPpl\MyApi2\Model\EpsApiMyApi2WebModelsAccessPointAccessPointModel::class );
            $data = \PPLShipping\Serializer::getInstance()->denormalize($data, \PPLShipping\Model\Model\ParcelAddressModel::class);
            $data = \PPLShipping\Serializer::getInstance()->denormalize($data, \PPLParcel::class);
        }

        $pplcart = PPLCart::getParcelByCartId($cartId);
        if ($pplcart && !$data)
        {
            $pplcart->delete();
        } else if ($data) {
            $pplcart = $pplcart ?: new PPLCart();
            $data->save();
            $pplcart->id_cart = $cartId;
            $pplcart->id_ppl_parcel = $data->id;
            $pplcart->save();
        }


        $carrier = (new Carrier(Context::getContext()->cart->id_carrier));
        die($this->module->hookDisplayCarrierExtraContent([
            "cart"=> Context::getContext()->cart,
            "carrier" =>['id_reference' => $carrier->id_reference]
        ]));


    }
}