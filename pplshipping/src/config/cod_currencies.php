<?php

return call_user_func(function() {
    $value = Configuration::getGlobalValue("PPLCodCurrencies");
    if ( !defined("PPL_REFRESH") && $value && ($value = @json_decode($value, true)) && $value) {
        return $value;
    }

    try {
        $cpl = new \PPLShipping\CPLOperation();
        $currencies = $cpl->getCodCurrencies();
        if ($currencies) {
            Configuration::updateGlobalValue("PPLCodCurrencies", json_encode($currencies));
            return $currencies;
        }
    }
    catch (\Exception $ex)
    {
        return [];
    }

    return  [];
});