<?php

use PPLShipping\Setting\CountrySetting;
use PPLShipping\Model\Model\OrderStateModel;
use PPLShipping\Serializer;
use PPLShipping\Setting\MethodSetting;
use PPLShipping\Model\Model\CurrencyModel;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;


class AdminCodelistPPLController extends AdminPPLController
{
    public $ajax = true;

    public function GetMethods(Request $request)
    {
        $token = $request->query->get("_token");
        if (!$this->isTokenValid($token)) {
            return $this->send403();
        }

        $output = [];

        foreach (MethodSetting::getMethods() as $method)
        {

            $output[] = pplcz_normalize($method);
        }

        return new JsonResponse($output);
    }

    public function GetCurrencies(Request $request)
    {
        $token = $request->query->get("_token");
        if (!$this->isTokenValid($token)) {
            return $this->send403();
        }

        $currencies = \Currency::getCurrencies();
        $output = [];

        foreach ($currencies as $key=>$value)
        {
            $currency = new CurrencyModel();
            $currency->setCode($value["iso_code"]);
            $currency->setTitle($value["name"]);
            $output[$key] = pplcz_normalize($currency);
        }

        return new JsonResponse($output);
    }

    public function GetOrderStates(Request $request)
    {
        $token = $request->query->get("_token");
        if (!$this->isTokenValid($token)) {
            return $this->send403();
        }

        $output = [];

        $defaultLangId = Configuration::get('PS_LANG_DEFAULT');
        $states = OrderState::getOrderStates($defaultLangId);

        foreach ($states as $key => $state)
        {
            $orderstatus = new OrderStateModel();
            $orderstatus->setCode($state['id_order_state']);
            $orderstatus->setTitle($state['name']);
            $output[$key] = pplcz_normalize($orderstatus);
        }
        return  new JsonResponse($output);
    }

    public function GetCountries(Request $request)
    {
        $token = $request->query->get("_token");
        if (!$this->isTokenValid($token)) {
            return $this->send403();
        }

        $output = CountrySetting::getCountries();

        $output = array_map(function($item) {
            return pplcz_normalize($item);
            }, $output);

        return new JsonResponse($output);
    }

}