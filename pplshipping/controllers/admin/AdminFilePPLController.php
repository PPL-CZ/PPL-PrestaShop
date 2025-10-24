<?php

use Symfony\Component\HttpFoundation\Request;

class AdminFilePPLController extends  AdminPPLController
{
    public function Download($batchRemoteId, Request $request)
    {
        if ($this->getToken() !== $request->query->get("_token"))
            return $this->send403();

        $cpl = (new \PPLShipping\CPLOperation());
        $packageReference = null;
        $packageNumber = null;
        $printFormat = strip_tags($request->query->get("print")) ?: \Configuration::getGlobalValue("PPLPrintSetting");

        $shipments= \PPLShipment::findRemoteBatchShipments($batchRemoteId);
        if ($printFormat)
            foreach ($shipments as $shipment)
            {
                $shipment->print_state = $printFormat;
                $shipment->save();
            }

        if ($printFormat)
            \Configuration::updateGlobalValue("PPLPrintSetting", $printFormat ?: "1/PDF");


        if ($request->query->get("shipmentId")) {
            $pplshipment = new \PPLShipment($request->query->get("shipmentId"));
            if ($pplshipment->id && $pplshipment->reference_id) {
                $packageReference = $pplshipment->reference_id;
                $printFormat = $pplshipment->print_state;
            }
        }
        if ($request->query->get("packageId"))
        {
            $package = new \PPLPackage($request->query->get("packageId"));
            if ($package->id && $package->shipment_number)
            {
                $packageNumber = $package->shipment_number;
            }
        }

        $cpl->getLabelContents($batchRemoteId, $packageReference, $packageNumber, $printFormat);
        exit;
    }

}