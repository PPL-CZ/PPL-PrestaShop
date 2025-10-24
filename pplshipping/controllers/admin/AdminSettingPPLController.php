<?php

use PPLShipping\CPLOperation;
use PPLShipping\Errors;
use PPLShipping\Model\Model\SenderAddressModel;
use PPLShipping\Serializer;
use PPLShipping\Validator;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use PPLShipping\Model\Model\ParcelPlacesModel;

class AdminSettingPPLController extends AdminPPLController
{
    public $ajax = true;

    public $multishop_context = Shop::CONTEXT_ALL;

    public function GetApi(Request $request)
    {
        $token = $request->query->get("_token");
        if ($token !== $this->getToken()) {
            return $this->send403();
        }


        $client_id = Configuration::getGlobalValue("PPLClientId");
        $client_secret = Configuration::getGlobalValue("PPLClientSecret");

        $api = new \PPLShipping\Model\Model\MyApi2();
        $api->setClientId($client_id);
        $api->setClientSecret($client_secret);

        return new JsonResponse(pplcz_normalize($api));
    }

    public function SetApi(Request $request)
    {
        $json = $this->getJson($request);
        /**
         * @var \PPLShipping\Model\Model\MyApi2 $myApi
         */
        $myApi = pplcz_denormalize($json, \PPLShipping\Model\Model\MyApi2::class);

        $error = pplcz_validate($myApi);
        if ($error->errors)
            return $this->send400($error);

        try {
            \PPLShipping\Setting\ApiSetting::setApi($myApi);
            $cpl = new \PPLShipping\CPLOperation();
            $cpl->clearAccessToken();
            $accessToken = $cpl->getAccessToken();
            if (!$accessToken) {
                http_response_code(400);
                die(json_encode("Nelze se pomocí zadaných údajů přihlásit"));
            }

            return  new Response("", 204);

        }
        catch (\Exception $ex)
        {
            throw $ex;
        }
    }

    public function GetShopGroups(Request $request)
    {
        $token = $request->query->get("_token");
        if ($token !== $this->getToken()) {
            return new \Symfony\Component\HttpFoundation\Response("", 403);
        }

        $groups = ShopGroup::getShopGroups(true);
        $output = [];
        foreach ($groups as $key => $value) {
            $output[$key] = pplcz_denormalize($value, \PPLShipping\Model\Model\ShopGroupModel::class);
            $output[$key] = pplcz_normalize($output[$key]);
        }
        return new JsonResponse($output);
    }


    public function GetAddresses(Request $request)
    {

        $token = $request->query->get("_token");
        if ($token !== $this->getToken()) {
            return $this->send403();
        }

        $shop_id = $request->query->get("shop_id") ?: null;
        $shop_group_id = $request->query->get("shop_group_id") ?: null;

        $senders = \PPLAddress::get_default_sender_addresses($shop_group_id, $shop_id);
        foreach ($senders as $key => $value)
        {
            $senders[$key] = pplcz_denormalize($value, SenderAddressModel::class);
            $senders[$key] = pplcz_normalize($senders[$key], "array");
        }

        return  new JsonResponse($senders);
    }

    public function RemoveAddresses(Request $request)
    {
        $shop_id = $request->query->get("shop_id") ?: null;
        $shop_group_id = $request->query->get("shop_group_id") ?: null;
        $token = $request->query->get('_token');
        if ($token !== $this->getToken()) {
            return $this->send403();
        }
        \PPLAddress::clear_sender_addresses($shop_group_id, $shop_id);
        return new Response("", 204);
    }

    public function SetAddresses(Request $request)
    {
        $shop_id = $request->query->get("shop_id") ?: null;
        $shop_group_id = $request->query->get("shop_group_id") ?: null;

        $sender = $this->getJson($request);
        $validator = Validator::getInstance();
        $errors = new Errors();

        foreach ($sender as $key => $value) {
            $sender[$key] = pplcz_denormalize($value, SenderAddressModel::class);
            $validator->validate($sender[$key], $errors, "$key");
        }

        if ($errors->errors)
        {
            return $this->send400($errors);
        }

        foreach ($sender as $key => $value)
        {
            $addressId = $sender[$key]->getId();
            $address = new \PPLAddress($addressId > 0 ? $addressId : null);
            $sender[$key] = pplcz_denormalize($sender[$key], \PPLAddress::class, ['data' => $address]);
            $sender[$key]->save();
        }

        \PPLAddress::set_default_sender_addresses($sender, $shop_group_id, $shop_id);
        return new Response("", 204);
    }

    public function GetPrint(Request $request)
    {
        $token = $request->query->get('_token');
        if ($token !== $this->getToken()) {
            return $this->send403();
        }

        $printSetting = \Configuration::getGlobalValue("PPLPrintSetting") ?: "1/PDF";
        $format = (new CPLOperation())->getFormat($printSetting);
        return new JsonResponse($format);
    }

    public function SetPrint(Request $request)
    {
        $content = $this->getJson($request);
        if (is_array($content))
        {
            if (!isset($content['shipmentId']) || !$content['shipmentId']) {
                foreach (['format', 'value', 'printState'] as $key)
                {
                    if (isset($content[$key])) {
                        $content = $content[$key];
                        break;
                    }
                }
            }
        }


        $printers = (new \PPLShipping\CPLOperation())->getAvailableLabelPrinters();
        if (is_string($content)) {
            foreach ($printers as $v) {
                if ($v->getCode() === $content) {
                    \Configuration::updateGlobalValue("PPLPrintSetting", $content);
                    return new Response("", 204);
                }
            }
        }
        else if (is_array($content))
        {
            pplcz_set_shipment_print($content['shipmentId'], $content['value']);
            return new Response("", 204);
        }
        return new Response("", 400);
    }


    public function  GetAvailablePrinters(Request $request)
    {
        $token = $request->query->get('_token');
        if ($token !== $this->getToken()) {
            return $this->send403();
        }

        $items = array_map(function ($item) {
            return pplcz_normalize($item);
        }, (new \PPLShipping\CPLOperation())->getAvailableLabelPrinters());
        return new JsonResponse($items);
    }

    public function GetShipmentPhases(Request $request)
    {
        $token = $request->query->get('_token');
        if ($token !== $this->getToken()) {
            return $this->send403();
        }

        $phases = array_map(function ($item) {
            $phase =  pplcz_denormalize($item, \PPLShipping\Model\Model\ShipmentPhaseModel::class);
            return $phase;
        }, pplcz_get_phases());

        $maxSync = pplcz_get_phase_max_sync();

        $sync = new \PPLShipping\Model\Model\SyncPhasesModel();
        $sync->setMaxSync($maxSync);
        $sync->setPhases($phases);

        return new JsonResponse(pplcz_normalize($sync));
    }

    public function SetPhase(Request $request)
    {
        $data = $this->getJson($request);
        /**
         * @var \PPLShipping\Model\Model\UpdateSyncPhasesModel $value
         */
        $value = Serializer::getInstance()->denormalize($data, \PPLShipping\Model\Model\UpdateSyncPhasesModel::class);
        if ($value->isInitialized("phases")) {
            foreach ($value->getPhases() as $phase) {
                pplcz_set_phase($phase->getCode(), $phase->getWatch(), $phase->getOrderState());
            }
        }
        if ($value->isInitialized("maxSync"))
        {
            pplcz_set_phase_max_sync($value->getMaxSync());
        }
        return new Response("", 204);
    }

    public function GetCarriers(Request $request)
    {
        $token = $request->query->get('_token');
        if ($token !== $this->getToken()) {
            return $this->send403();
        }

        $carriers = [];
        foreach (\Carrier::getCarriers(0, false, false, null, null, \Carrier::ALL_CARRIERS) as $carrier) {
            $carrier  = new \Carrier($carrier['id_carrier']);
            $model = pplcz_denormalize($carrier, \PPLShipping\Model\Model\PrestaCarrierModel::class);
            $model = pplcz_normalize($model);
            $carriers[] = $model;
        }

        return new JsonResponse($carriers);
    }

    public function PutCarrier(Request $request)
    {
        $data = $this->getJson($request);
        /**
         * @var \PPLShipping\Model\Model\UpdatePrestaCarrierModel $update
         */
        $update = Serializer::getInstance()->denormalize($data, \PPLShipping\Model\Model\UpdatePrestaCarrierModel::class);

        $carrier_id = $update->getCarrierId();
        $code = $update->getServiceCode();
        $carrier = \Carrier::getCarrierByReference($carrier_id);

        if ($carrier->external_module_name && $carrier->external_module_name !== "pplshipping")
        {
            return $this->send400();
        }

        if ($code)
        {
            $carrier->external_module_name = "pplshipping";
            $carrier->is_module = true;
            $carrier->shipping_external = true;
            $carrier->range_behavior = true;
            $carrier->need_range = true;
            Configuration::updateGlobalValue("PPLCarrier{$carrier->id_reference}", $code, false);
        } else {
            $carrier->external_module_name = null;
            $carrier->is_module = false;
            $carrier->shipping_external = false;
            $carrier->range_behavior = false;
            Configuration::deleteByName("PPLCarrier{$carrier->id_reference}");
        }
        $carrier->save();
        return new Response("", 204);
    }


    public function GetParcelPlaces(Request $request) {
        $token = $request->query->get('_token');
        if ($token !== $this->getToken()) {
            return $this->send403();
        }

        $configuration = new \Configuration();
        $parcelplaces = pplcz_denormalize($configuration, ParcelPlacesModel::class);

        return new JsonResponse(pplcz_normalize($parcelplaces));
    }

    public function SetParcelPlaces(Request $request)
    {
        $data = $this->getJson($request);
        $update = pplcz_denormalize($data, ParcelPlacesModel::class);
        pplcz_denormalize($update, \Configuration::class);
        return new Response("", 204);
    }

    public function RefreshKey(Request $request)
    {
        if ($request->query->get("_token") !== $this->getToken())
            return $this->send403();

        $bytes = openssl_random_pseudo_bytes(16);  // 16 bytů = 128 bitů
        $secure_key = bin2hex($bytes);
        Configuration::updateGlobalValue("PPLShipmentCronKey", $secure_key);

        return new JsonResponse(\Context::getContext()->link->getModuleLink('pplshipping', 'CronPPL', [
            "secure_key" => $secure_key
        ]));

    }
}