<?php

namespace PPLShipping\ModelNormalizer;


use PPLShipping\Errors;
use PPLShipping\Model\Model\ShipmentModel;
use PPLShipping\Model\Model\ShipmentWithAdditionalModel;
use PPLShipping\Model\Model\WpErrorModel;
use PPLShipping\Validator\WP_Error;
use PPLShipping\Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

class ShipmentWithAdditionalModelDenormalizer implements DenormalizerInterface
{

    public function denormalize($data, string $type, ?string $format = null, array $context = [])
    {
        $shipmentModel = pplcz_denormalize($data, ShipmentModel::class);

        $shipment = new ShipmentWithAdditionalModel();
        $shipment->setShipment($shipmentModel);


        $errors = pplcz_validate($shipmentModel);

        if ($errors->errors)
        {
            $errors = pplcz_denormalize($errors, WpErrorModel::class . '[]');
            $shipment->setErrors($errors);
        }

        return $shipment;
    }

    public function supportsDenormalization($data, string $type, ?string $format = null)
    {
        if (($data instanceof \PPLShipment || $data instanceof \Order)
            && $type === ShipmentWithAdditionalModel::class)
        {
            return true;
        }
        return false;
    }
}