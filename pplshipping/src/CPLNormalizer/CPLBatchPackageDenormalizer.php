<?php
namespace PPLShipping\CPLNormalizer;

use PluginPpl\MyApi2\Model\EpsApiMyApi2WebModelsShipmentBatchExternalNumberModel;
use PluginPpl\MyApi2\Model\EpsApiMyApi2WebModelsShipmentBatchShipmentModelShipmentSet;
use PluginPpl\MyApi2\Model\EpsApiMyApi2WebModelsShipmentBatchShipmentSetItemModel;
use PluginPpl\MyApi2\Model\EpsApiMyApi2WebModelsShipmentBatchShipmentSetItemModelWeighedShipmentInfo;
use PPLShipping\Serializer;
use PPLShipping\Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

class CPLBatchPackageDenormalizer implements DenormalizerInterface
{

    public function denormalize($data, string $type, ?string $format = null, array $context = [])
    {
        if ($data instanceof \PPLShipment && $type === EpsApiMyApi2WebModelsShipmentBatchShipmentModelShipmentSet::class)
        {
            $shipmentSet = new EpsApiMyApi2WebModelsShipmentBatchShipmentModelShipmentSet();
            $ids = $data->get_package_ids();
            $shipmentSet->setNumberOfShipments(count($ids));
            foreach ($ids as $key => $id)
            {
                $package = new \PPLPackage($id);
                $ids[$key] = Serializer::getInstance()->denormalize($package, EpsApiMyApi2WebModelsShipmentBatchShipmentSetItemModel::class);
            }
            $shipmentSet->setShipmentSetItems($ids);
            return $shipmentSet;
        }
        else if ($data instanceof \PPLPackage && $type === EpsApiMyApi2WebModelsShipmentBatchShipmentSetItemModel::class)
        {
            $shipment = new EpsApiMyApi2WebModelsShipmentBatchShipmentSetItemModel();

            if ($data->weight) {
                $info = new EpsApiMyApi2WebModelsShipmentBatchShipmentSetItemModelWeighedShipmentInfo();
                $info->setWeight($data->weight);
                $shipment->setWeighedShipmentInfo($info);
            }
            if ($data->insurance)
                $shipment->setInsurance($data->insurance);

            if ($data->reference_id)
            {
                $externalNumber = new EpsApiMyApi2WebModelsShipmentBatchExternalNumberModel();
                $externalNumber->setCode("CUST");
                $externalNumber->setExternalNumber($data->reference_id);
                $shipment->setExternalNumbers([$externalNumber]);
            }
            return $shipment;
        }
        throw new \Exception();
    }

    public function supportsDenormalization($data, string $type, ?string $format = null)
    {
        return $data instanceof \PPLShipment && $type === EpsApiMyApi2WebModelsShipmentBatchShipmentModelShipmentSet::class
                || $data instanceof \PPLPackage && $type === EpsApiMyApi2WebModelsShipmentBatchShipmentSetItemModel::class;

    }
}