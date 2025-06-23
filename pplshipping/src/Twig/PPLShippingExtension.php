<?php
namespace PPLShipping\Twig;

use PPLShipping\Model\Model\ShipmentModel;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class PPLShippingExtension extends  AbstractExtension
{
    public function getFunctions()
    {
        return [
           new TwigFunction( "get_ppl_shipment", function ($id) {
               $shipments = \PPLShipment::findShipmentsByOrderID($id);
               if (!$shipments)
                return [pplcz_denormalize(new \Order($id), ShipmentModel::class)];
               return array_map(function ($item) {
                   return pplcz_denormalize($item, ShipmentModel::class);
               },$shipments);
           }),
            new TwigFunction( "ppl_validate_shipment", function (ShipmentModel $shipment) {
                return pplcz_validate($shipment, "")->errors;
            })
        ];
    }
}