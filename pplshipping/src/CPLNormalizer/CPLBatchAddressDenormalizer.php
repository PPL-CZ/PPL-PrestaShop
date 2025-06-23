<?php
namespace PPLShipping\CPLNormalizer;

use PluginPpl\MyApi2\Model\EpsApiMyApi2WebModelsShipmentBatchDormantShipmentModelRecipient;
use PluginPpl\MyApi2\Model\EpsApiMyApi2WebModelsShipmentBatchRecipientAddressModel;
use PluginPpl\MyApi2\Model\EpsApiMyApi2WebModelsShipmentBatchShipmentModelSender;
use PPLShipping\Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

class CPLBatchAddressDenormalizer implements DenormalizerInterface
{

    public function denormalize($data, string $type, ?string $format = null, array $context = [])
    {
        if ($data instanceof \PPLAddress && $type === EpsApiMyApi2WebModelsShipmentBatchShipmentModelSender::class) {
            $sender = new EpsApiMyApi2WebModelsShipmentBatchShipmentModelSender();
            $sender->setName($data->name);
            $sender->setCity($data->city);
            $sender->setStreet($data->street);
            $sender->setZipCode($data->zip);
            $sender->setEmail($data->mail);
            $sender->setPhone($data->phone);
            $sender->setCountry($data->country);
            $sender->setContact($data->contact);
            return $sender;
        }

        if ($data instanceof \PPLAddress && $type === EpsApiMyApi2WebModelsShipmentBatchRecipientAddressModel::class) {
            $recepient = new EpsApiMyApi2WebModelsShipmentBatchRecipientAddressModel();
            $recepient->setName($data->name);
            $recepient->setPhone($data->phone);
            $recepient->setEmail($data->mail);
            $recepient->setCity($data->city);
            $recepient->setZipCode($data->zip);
            $recepient->setStreet($data->street);
            $recepient->setCountry($data->country);
            return $recepient;
        }
        throw new \Exception();
    }

    public function supportsDenormalization($data, string $type, ?string $format = null)
    {
        return $data instanceof \PPLAddress && $type === EpsApiMyApi2WebModelsShipmentBatchShipmentModelSender::class
            || $data instanceof \PPLAddress && $type === EpsApiMyApi2WebModelsShipmentBatchRecipientAddressModel::class;
    }
}