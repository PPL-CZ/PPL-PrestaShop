<?php



return call_user_func(function() {

    $statuses = Configuration::getGlobalValue("PPLShipmentStatuses") ?: "[]";
    $statuses = @json_decode($statuses, true);

    if (!$statuses || defined("PPL_REFRESH") )
    {
        try {
            $cpl = new PPLShipping\CPLOperation();
            $statuses = $cpl->getStatuses();
            if ($statuses) {
                Configuration::updateGlobalValue("PPLShipmentStatuses", json_encode($statuses));
                return $statuses;
            }
        }
        catch (Exception $ex)
        {
        }
    }

    return $statuses;
});