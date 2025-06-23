<?php
namespace PPLShipping\CPLNormalizer;

use PluginPpl\MyApi2\Model\EpsApiMyApi2WebModelsShipmentBatchCreateShipmentBatchModel;
use PluginPpl\MyApi2\Model\EpsApiMyApi2WebModelsShipmentBatchCreateShipmentBatchModelLabelSettings;
use PluginPpl\MyApi2\Model\EpsApiMyApi2WebModelsShipmentBatchLabelSettingsModelCompleteLabelSettings;
use PluginPpl\MyApi2\Model\EpsApiMyApi2WebModelsShipmentBatchShipmentModel;
use PPLShipping\Serializer;
use PPLShipping\Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

class CPLBatchCreateShipmentsDenormalizer implements DenormalizerInterface
{

    public function denormalize($data, string $type, ?string $format = null, array $context = [])
    {
        $createShipment = new EpsApiMyApi2WebModelsShipmentBatchCreateShipmentBatchModel();
        $createShipment->setShipments(array_map(function ($item) {
            if (!($item instanceof \PPLShipment))
                $item = new \PPLShipment($item);
            if (!$item->id)
                throw new \Exception("problem se zasilkou");
            return Serializer::getInstance()->denormalize($item, EpsApiMyApi2WebModelsShipmentBatchShipmentModel::class);
        }, $data));

        $batch = new EpsApiMyApi2WebModelsShipmentBatchLabelSettingsModelCompleteLabelSettings();
        $batch->setIsCompleteLabelRequested(true);
        $batch->setPageSize("A4");
        $batch->setPosition(1);

        $batchModelLabelSetting = new EpsApiMyApi2WebModelsShipmentBatchCreateShipmentBatchModelLabelSettings();
        $batchModelLabelSetting->setCompleteLabelSettings($batch);
        $batchModelLabelSetting->setFormat("Pdf");
        $createShipment->setLabelSettings($batchModelLabelSetting);
        return $createShipment;

    }

    public function supportsDenormalization($data, string $type, ?string $format = null)
    {
        return $type === EpsApiMyApi2WebModelsShipmentBatchCreateShipmentBatchModel::class
                && is_array($data);
    }
}