<?php
namespace PPLShipping\ModelNormalizer;

use PluginPpl\MyApi2\Model\EpsApiMyApi2WebModelsAccessPointAccessPointModel;
use PPLShipping\Model\Model\ParcelAddressModel;
use PPLShipping\Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

class ParcelAddressModelDenormalizer implements DenormalizerInterface {


    public function denormalize($data, string $type, ?string $format = null, array $context = [])
    {
        if ($data instanceof \Cart && ParcelAddressModel::class === $type) {
            $id = $data->id;
            $parcel = \PPLParcel::getParcelByCartId($id);
            return self::denormalize($parcel, $type);
        }
        else if ($data instanceof \PPLParcel && ParcelAddressModel::class === $type)
        {
            $parcel = new ParcelAddressModel();
            $parcel->setCode($data->code);
            $parcel->setRemoteId($data->remote_id);
            $parcel->setName($data->name);
            $parcel->setName2($data->name2);
            $parcel->setId($data->id);
            $parcel->setZip($data->zip);
            $parcel->setCountry($data->country);
            $parcel->setLat($data->lat);
            $parcel->setLng($data->lng);
            $parcel->setCity($data->city);
            $parcel->setStreet($data->street);
            $parcel->setType($data->type);
            return $parcel;
        }
        else if ($data instanceof EpsApiMyApi2WebModelsAccessPointAccessPointModel)
        {
            $parcel = new ParcelAddressModel();
            $parcel->setCode($data->getAccessPointCode());
            $parcel->setType($data->getAccessPointType());
            $parcel->setRemoteId($data->getAccessPointCode());
            $parcel->setName($data->getName());
            $parcel->setName2($data->getName2());
            $parcel->setZip($data->getZipCode());
            $parcel->setCountry($data->getCountry());
            $parcel->setLat($data->getGps()->getLatitude());
            $parcel->setLng($data->getGps()->getLongitude());
            $parcel->setCity($data->getCity());
            $parcel->setStreet($data->getStreet());
            return $parcel;
        }
        else if ($data instanceof ParcelAddressModel && $type === \PPLParcel::class)
        {
            $parcel = null;
            if (isset($context['data']) && $context['data'])
                $parcel = $context['data'];
            if (!$parcel)
                $parcel = \PPLParcel::getParcelByRemoteId($data->getRemoteId());
            if (!$parcel)
                $parcel = new \PPLParcel();

            $parcel->city = $data->getCity();
            $parcel->street = $data->getStreet();
            $parcel->lng = $data->getLng();
            $parcel->lat = $data->getLat();
            $parcel->name = $data->getName();
            $parcel->name2 = $data->getName2();
            $parcel->remote_id = $data->getRemoteId();
            $parcel->country = $data->getCountry();
            $parcel->zip = $data->getZip();
            $parcel->code = $data->getCode();
            $parcel->type = $data->getType();

            return $parcel;

        }
        return null;

    }

    public function supportsDenormalization($data, string $type, ?string $format = null)
    {
        return (($data instanceof \Cart || $data instanceof \PPLParcel || $data instanceof EpsApiMyApi2WebModelsAccessPointAccessPointModel) && ($type === ParcelAddressModel::class))
                || $data instanceof ParcelAddressModel && $type = \PPLParcel::class;

    }
}