<?php
namespace PPLShipping\CPLNormalizer;

use PluginPpl\MyApi2\Model\EpsApiMyApi2WebModelsShipmentBatchRecipientAddressModel;
use PluginPpl\MyApi2\Model\EpsApiMyApi2WebModelsShipmentBatchShipmentModel;
use PluginPpl\MyApi2\Model\EpsApiMyApi2WebModelsShipmentBatchShipmentModelCashOnDelivery;
use PluginPpl\MyApi2\Model\EpsApiMyApi2WebModelsShipmentBatchShipmentModelInsurance;
use PluginPpl\MyApi2\Model\EpsApiMyApi2WebModelsShipmentBatchShipmentModelSender;
use PluginPpl\MyApi2\Model\EpsApiMyApi2WebModelsShipmentBatchShipmentModelShipmentSet;
use PluginPpl\MyApi2\Model\EpsApiMyApi2WebModelsShipmentBatchShipmentModelSpecificDelivery;
use PPLShipping\Serializer;
use PPLShipping\Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

class CPLBatchShipmentDenormalizer implements DenormalizerInterface
{
    public const INTEGRATOR = "4542104";

    public function denormalize($data, string $type, ?string $format = null, array $context = [])
    {
        if ($data instanceof \PPLShipment)
        {
            if ($type === EpsApiMyApi2WebModelsShipmentBatchShipmentModel::class) {
                $shipmentBatch = new EpsApiMyApi2WebModelsShipmentBatchShipmentModel();
                $shipmentBatch->setReferenceId($data->reference_id);
                $shipmentBatch->setProductType($data->service_code);
                if ($data->cod_value) {
                    $shipmentBatch->setCashOnDelivery(Serializer::getInstance()->denormalize($data, EpsApiMyApi2WebModelsShipmentBatchShipmentModelCashOnDelivery::class));
                }
                $shipmentBatch->setIntegratorId(self::INTEGRATOR);
                $shipmentBatch->setReferenceId($data->reference_id);
                $shipmentBatch->setNote($data->note);
                $shipmentBatch->setSender(Serializer::getInstance()->denormalize(new \PPLAddress($data->id_sender_address), EpsApiMyApi2WebModelsShipmentBatchShipmentModelSender::class));
                $shipmentBatch->setRecipient(Serializer::getInstance()->denormalize(new \PPLAddress($data->id_recipient_address), EpsApiMyApi2WebModelsShipmentBatchRecipientAddressModel::class));

                if ($data->has_parcel)
                    $shipmentBatch->setSpecificDelivery(Serializer::getInstance()->denormalize($data, EpsApiMyApi2WebModelsShipmentBatchShipmentModelSpecificDelivery::class ));
                if ($data->age)
                    $shipmentBatch->setAgeCheck($data->age);

                $shipmentBatch->setShipmentSet(Serializer::getInstance()->denormalize($data, EpsApiMyApi2WebModelsShipmentBatchShipmentModelShipmentSet::class));

                return $shipmentBatch;
            }
            else if ($type === EpsApiMyApi2WebModelsShipmentBatchShipmentModelCashOnDelivery::class)
            {
                $cashOnDelivery = new EpsApiMyApi2WebModelsShipmentBatchShipmentModelCashOnDelivery();
                $cashOnDelivery->setCodVarSym($data->cod_variable_number);
                $cashOnDelivery->setCodCurrency($data->cod_value_currency);
                $cashOnDelivery->setCodPrice($data->cod_value);
                return $cashOnDelivery;
            }
            else if ($type === EpsApiMyApi2WebModelsShipmentBatchShipmentModelSpecificDelivery::class )
            {
                $specific = new EpsApiMyApi2WebModelsShipmentBatchShipmentModelSpecificDelivery();
                $parcel = new \PPLParcel($data->id_parcel);
                $specific->setParcelShopCode($parcel->remote_id);
                return $specific;
            }
            else if ($type === EpsApiMyApi2WebModelsShipmentBatchShipmentModelInsurance::class) {
                $ids = $data->get_package_ids();
                $id = reset($ids);
                $package = new \PPLPackage($id);
                if ($package->insurance) {
                    $insurance = new EpsApiMyApi2WebModelsShipmentBatchShipmentModelInsurance();
                    $insurance->setInsurancePrice($package->insurance);
                    return $insurance;
                }
                return null;
            }


        }
        throw new \Exception();
    }

    public function supportsDenormalization($data, string $type, ?string $format = null)
    {
        return $data instanceof \PPLShipment && $type === EpsApiMyApi2WebModelsShipmentBatchShipmentModel::class
                || $data instanceof \PPLShipment && $type === EpsApiMyApi2WebModelsShipmentBatchShipmentModelCashOnDelivery::class
                || $data instanceof \PPLShipment && $type === EpsApiMyApi2WebModelsShipmentBatchShipmentModelSpecificDelivery::class
                || $data instanceof \PPLShipment && $type === EpsApiMyApi2WebModelsShipmentBatchShipmentModelInsurance::class;
    }
}