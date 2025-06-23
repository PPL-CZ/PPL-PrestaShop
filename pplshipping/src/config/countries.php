<?php


return call_user_func(function() {

    $countries = Configuration::getGlobalValue("PPLCountries");
    $countries = @json_decode($countries, true);

    if (!$countries || defined("PPL_REFRESH")) {
        try {
            $cpl = new PPLShipping\CPLOperation();
            $countries = $cpl->getCountries();
            if ($countries) {
                Configuration::updateGlobalValue("PPLCountries", $countries);
                return $countries;
            }
        }
        catch (Exception $ex)
        {

        }
    }

    return [
        'CZ' => true,
        'DE' => false,
        'GB' => false,
        'SK' => true,
        'AT' => false,
        'PL' => true,
        'CH' => false,
        'FI' => false,
        'HU' => true,
        'SI' => false,
        'LV' => false,
        'EE' => false,
        'LT' => false,
        'BE' => false,
        'DK' => false,
        'ES' => false,
        'FR' => false,
        'IE' => false,
        'IT' => false,
        'NL' => false,
        'NO' => false,
        'PT' => false,
        'SE' => false,
        'RO' => true,
        'BG' => false,
        'GR' => false,
        'LU' => false,
        'HR' => false,
    ];
});