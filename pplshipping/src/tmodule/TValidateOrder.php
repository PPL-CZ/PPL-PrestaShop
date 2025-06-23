<?php
namespace  PPLShipping\tmodule;

use PPLShipping\Model\Model\CartModel;
use PPLShipping\Serializer;

trait  TValidateOrder
{

    public function hookActionFilterDeliveryOptionList($params)
    {
        if (isset($params['delivery_option_list'])) {
            foreach ($params['delivery_option_list'] as $key => $carries) {
                foreach ($carries as $ref_id => $carrierId) {
                    $carrier = new \Carrier($ref_id);
                    if (!$carrier || $carrier->external_module_name !== "pplshipping")
                        continue;
                    /**
                     * @var CartModel $cart
                     */
                    $cart = pplcz_denormalize($params['cart'], CartModel::class, ["carrier" => $carrier]);
                    if ($cart->getDisabledByRules() || $cart->getDisabledByCountry() || $cart->getDisabledByProduct())
                        unset($params['delivery_option_list'][$key][$ref_id]);
                }
            }
        }
        else if (isset($params['delivery_option_list_presta17']))
        {
            foreach ($params['delivery_option_list_presta17'] as $ref_id => $carrierId) {
                $carrier = new \Carrier($ref_id);
                if (!$carrier || $carrier->external_module_name !== "pplshipping")
                    continue;
                /**
                 * @var CartModel $cart
                 */
                $cart = pplcz_denormalize($params['cart'], CartModel::class, ["carrier" => $carrier]);
                if ($cart->getDisabledByRules() || $cart->getDisabledByCountry() || $cart->getDisabledByProduct())
                    unset($params['delivery_option_list_presta17'][$ref_id]);
            }
        }
        return;
    }
    
    public function hookActionValidateStepComplete($params)
    {
        if ($params['step_name'] === 'delivery')
        {
            /**
             * @var CartModel $cartModel
             */

            $errors = pplcz_validate($params['cart'], "");
            if ($errors->errors)
            {
                foreach ($errors->errors as $key => $value)
                {
                    foreach ($value as $val)
                        $this->context->controller->errors[] = $this->l($val);
                }
                $params['completed'] = false;
            }
        }
    }

    public function hookActionBeforeCreateOrderCart ($params)
    {
        /**
         * @var CartModel $cartModel
         */
        $errors = pplcz_validate($params['cart'], "");
        if ($errors->errors)
        {
            foreach ($errors->errors as $key => $value)
            {
                foreach ($value as $val)
                    $this->context->controller->errors[] = $this->l($val);
            }
        }
    }


    public function  hookActionValidateOrder($params)
    {

        $cart = $params['cart'];
        /**
         * @var CartModel $cartModel
         */
        $cartModel = Serializer::getInstance()->denormalize($cart, CartModel::class);
        if ($cartModel->getParcelRequired())
        {
            $pplparcel = \PPLParcel::getParcelByCartId($cart->id);
            $pplorder = new \PPLOrder();
            $pplorder->id_order = $params['order']->id;
            $pplorder->id_ppl_parcel = $pplparcel->id;
            $pplorder->save();
        } else {
            $pplorder = new \PPLOrder();
            $pplorder->id_order = $params['order']->id;
            $pplorder->save();
        }
        return;
    }
}