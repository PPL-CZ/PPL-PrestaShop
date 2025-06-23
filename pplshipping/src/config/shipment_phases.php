<?php



return call_user_func(function() {

    $phases = Configuration::getGlobalValue("PPLShipmentPhases");
    $phases = json_decode($phases, true);

    if (!$phases || defined("PPL_REFRESH") )
    {
        try {
            $cpl = new PPLShipping\CPLOperation();
            $phases = $cpl->getShipmentPhases();
            if ($phases) {
                Configuration::updateGlobalValue("PPLShipmentPhases", json_encode($phases));
                return $phases;
            }
        }
        catch (\Exception $ex)
        {

        }
    }

    return [
        "Order" => "Objednávka",
        "InTransport" => "V přepravě",
        "Delivering" => "Na cestě",
        "PickupPoint" => "Na výdejním místě",
        "CodPayed" => "Zaplacená dobírka",
        "Delivered" => "Doručeno",
        "Returning"=> "Na cestě zpět odesílateli",
        "BackToSender" => "Vráceno odesílateli",
    ];
});