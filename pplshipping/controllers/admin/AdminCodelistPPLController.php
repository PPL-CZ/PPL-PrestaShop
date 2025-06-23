<?php

use PPLShipping\Serializer;
use Symfony\Component\HttpFoundation\Request;


class AdminCodelistPPLController extends AdminPPLController
{
    public $ajax = true;

    public function GetMethods(Request $request)
    {
        $token = $request->query->get("_token");
        if ($token !== $this->getToken()) {
            return $this->send403();
        }

        $iscod = (int)$request->query->get("withCod");

        $output = [];

        foreach (pplcz_get_all_services() as $key => $value)
        {
            if ($key === pplcz_get_cod_name($key) && !$iscod)
                continue;
            $data = new \PPLShipping\Model\Model\ShipmentMethodModel();
            $data->setCode($key);
            $data->setTitle($value);
            $data->setCodAvailable(!!pplcz_has_cod($key));
            $data->setParcelRequired(pplcz_parcel_required($key));
            $output[] = pplcz_normalize($data);
        }

        return $this->sendJsonModel(array_values($output));
    }

    public function GetCurrencies(Request $request)
    {
        $token = $request->query->get("_token");
        if ($token !== $this->getToken()) {
            return $this->send403();
        }
        $datacurrencies = \Currency::getCurrencies();
        $currencies = pplcz_get_cod_currencies();
        $resolved = [];
        foreach ($currencies as $key => $value)
        {
            $currency = new \PPLShipping\Model\Model\CurrencyModel();
            $country = $value['country'];
            $currency = $value['currency'];

            $hascurrency = array_values(array_filter($datacurrencies, function ($item) use($currency) {
                return $currency === $item['iso_code'];
            }));

            if ($hascurrency && !in_array($currency, $resolved, true)) {
                $resolved[] = $currency;
                $modelcurrency = new \PPLShipping\Model\Model\CurrencyModel();
                $modelcurrency->setCode($hascurrency[0]['iso_code']);
                $modelcurrency->setTitle($hascurrency[0]['name']);
                $currencies[$key] = pplcz_normalize($modelcurrency);
            }
            else
            {
                unset($currencies[$key]);
            }
        }
        return new \Symfony\Component\HttpFoundation\JsonResponse(array_values($currencies));

    }

    public function GetOrderStates()
    {
        $defaultLangId = Configuration::get('PS_LANG_DEFAULT');
        $states = OrderState::getOrderStates($defaultLangId);
        foreach ($states as $key => $state)
        {
            $states[$key] = [
                "code" => $state['id_order_state'],
                "title" => $state['name']
            ];
        }
        return  new \Symfony\Component\HttpFoundation\JsonResponse($states);
    }

    public function GetCountries(Request $request)
    {
        $currencies = pplcz_get_cod_currencies();

        $token = $request->query->get("_token");
        if ($token !== $this->getToken()) {
            return $this->send403();
        }

        $id_lang = \Context::getContext()->language->id;


        $allowed_countries = \Country::getCountries($id_lang, true);
        foreach ($allowed_countries as $key => $value)
        {
            $country = new \PPLShipping\Model\Model\CountryModel();
            $country->setCode($value['iso_code']);
            $country->setTitle($value['name']);
            $country->setParcelAllowed(in_array($value['iso_code'], ["CZ", "SK", "PL", "DE"]));


            $country->setCodAllowed(array_unique(array_map(function ($item) {
                return $item['currency'];

            }, array_filter($currencies, function ($item) use ($key) {
                return $item['country'] === $key;
            }))));


            $allowed_countries[$key] = Serializer::getInstance()->normalize($country);
        }

        return new \Symfony\Component\HttpFoundation\JsonResponse((array_values($allowed_countries)));
    }

}