<?php

use PPLShipping\CPLOperation;
use PPLShipping\Model\Model\PackageModel;
use PPLShipping\Model\Model\ShipmentModel;

function pplcz_create_name($name)
{
    return "pplcz_" . $name;
}

function pplcz_get_cod_currencies() {
    $currencies = include __DIR__ . '/config/cod_currencies.php';
    return $currencies ;
}

function pplcz_get_all_services() {
    return [
        "SMAR" => "PPL Parcel CZ Smart",
        "SMAD" => "PPL Parcel CZ Smart (dobírka)",
        "SBOX" => "PPL Parcel CZ Smart To Box",
        "SBOD" => "PPL Parcel CZ Smart To Box (dobírka)",
        "PRIV" => "PPL Parcel CZ Private",
        "PRID" => "PPL Parcel CZ Private (dobírka)",

        "SMEU" => "PPL Parcel Smart Europe",
        "SMED" => "PPL Parcel Smart Europe (dobírka)",
        "CONN" => "PPL Parcel Connect",
        "COND" => "PPL Parcel Connect (dobírka)"
    ];
}

function pplcz_get_services() {
    return [
        "SMAR" => "PPL Parcel CZ Smart",
        "PRIV" => "PPL Parcel CZ Private",
        "SBOX" => "PPL Parcel CZ Smart To Box",

        "SMEU" => "PPL Parcel Smart Europe",
        "CONN" => "PPL Parcel Connect"
    ];
}

function pplcz_parcel_required($code)
{
    return in_array($code, ["SMAD", "SMAR", "SMED","SMEU", "SBOX", "SBOD"], true);
}


function pplcz_has_cod($code)
{
    if (substr($code, -1) === "D")
        return true;
    return false;
}


function pplcz_get_cod_name($code)
{
    $services = pplcz_get_services();
    if (substr($code, -1) === "D")
        return $code;
    foreach ($services as $key=>$val) {
        if ($code === $key) {
            $asCod = substr($key, 0, 3) . 'D';
            return $asCod;
        }

    }
    return false;
}

function pplcz_get_parcel_countries()
{
    $allowed_countries = pplcz_get_allowed_countries();

    foreach ($allowed_countries as $key => $v) {
        $mapCountries = ['CZ', 'SK', 'PL', 'DE', 'NL', 'RO', 'BG', 'HU', 'AT'];
        if (!in_array($key, $mapCountries, true))
            unset($allowed_countries[$key]);
    }

    return $allowed_countries;
}

function pplcz_get_allowed_countries() {
    $id = Context::getContext()->language->id;

    $allowed_countries = Country::getCountries($id, true);
    $get_countries = [];
    foreach ($allowed_countries as $value)
    {
        $get_countries[$value['iso_code']] = $value['name'];
    }

    $countries = include __DIR__ . '/config/countries.php';
    foreach ($get_countries as $key => $v) {
        if (!isset($countries[$key]))
            unset($get_countries[$key]);
    }

    return $get_countries;
}

function pplcz_asset_icon($name) {
    return \Tools::getHttpHost(true) . __PS_BASE_URI__ . 'modules/pplshipping/assets/images/' . $name;
}


function pplcz_denormalize($data, string $type, array $context = [])
{
    return \PPLShipping\Serializer::getInstance()->denormalize( $data, $type, null, $context );
}

function pplcz_normalize($data, ?string $format = null, array $context = [])
{
    return \PPLShipping\Serializer::getInstance()->normalize( $data, $format, $context);
}

function pplcz_validate($data, $path = "", ?\PPLShipping\Errors $errors = null) {
    $errors = $errors ?: new \PPLShipping\Errors();
    \PPLShipping\Validator::getInstance()->validate($data, $errors, $path);
    return $errors;
}


function pplcz_set_shipment_print($shipmentId, $print)
{
    PPLShipment::set_print_state($shipmentId, $print);
}



function pplcz_get_map_args() {
    $lat = Tools::getValue('ppl_lat');
    $lng = Tools::getValue('ppl_lng');
    $withCard = Tools::getValue('ppl_withCard');
    $withCash = Tools::getValue('ppl_withCash');
    $country = Tools::getValue('ppl_country');
    $countries = Tools::getValue("ppl_countries");
    $address = Tools::getValue('ppl_address');
    $hiddenpoints = Tools::getValue("ppl_hiddenpoints");

    $parcelplaces = pplcz_denormalize(new \Configuration(), \PPLShipping\Model\Model\ParcelPlacesModel::class);
    $lang = $parcelplaces->getMapLanguage();
    if (!in_array($lang,[ "CS", "EN"], true))
        $lang = "CS";

    $data = [];

    $data['data-language'] = strtolower($lang);


    if (floatval($lat) && floatval($lng)) {
        $data["data-lat"] = $lat;
        $data["data-lng"] = $lng;
    }


    $data["data-initialfilters"] = [];

    if (intval($withCard))
        $data["data-initialfilters"][] = "CardPayment";
    if (intval($withCash))
        $data["data-initialfilters"][] = "ParcelShop";

    if (isset($data['data-initialfilters']) && !$data["data-initialfilters"]) {
        unset($data["data-initialfilters"]);
    } else {
        $data["data-initialfilters"] = join(',', $data["data-initialfilters"]);
    }
    if ($hiddenpoints)
        $data['data-hiddenpoints'] = $hiddenpoints;
    if ($countries)
        $data['data-countries'] = strtolower($countries);

    if (isset($data['data-lat']) && $data["data-lat"]) {
        $data["data-mode"] = "static";
    }

    if ($address)
    {
        $data["data-address"] = $address;
    }

    if ($country)
    {
        $data['data-country'] = $country;
    }
    return $data;
}


spl_autoload_register(function ($class) {
    switch ($class)
    {
        case "PPLAddress":
        case "PPLCart":
        case "PPLCollection":
        case "PPLPackage":
        case "PPLParcel":
        case "PPLOrder":
        case "PPLShipment":
        case "PPLBatch":
        case "PPLLog":
        case "PPLBaseDisabledRule":
            require_once  __DIR__ . '/../classes/' . $class. '.php';
            return;
        case 'AdminPPLController':
        case 'AdminCodelistPPLController':
        case 'AdminFilePPLController':
        case 'AdminSettingPPLController':
        case 'AdminShipmentBatchPPLController':
        case 'AdminShipmentPPLController':
        case 'AdminConfigurationPPLController':
        case 'AdminOrderPPLController':
        case 'AdminCollectionPPLController':
        case 'AdminLogPPLController':
            require_once  __DIR__ . '/../controllers/admin/' . $class . '.php';
            return;
    }
});

