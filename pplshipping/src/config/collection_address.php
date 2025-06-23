<?php

return call_user_func(function() {


    $address = null;
    try {
        $address = Configuration::getGlobalValue("PPLCollectionAddress");
        $address = @json_decode($address, true);
        if (!is_array($address))
            $address = null;
    }
    catch (Exception $ex)
    {

    }

    try {
        if (!$address || defined("PPL_REFRESH")) {
            $cpl = new PPLShipping\CPLOperation();
            $collectionAddresses = $cpl->getCollectionAddresses();

            foreach ($collectionAddresses as $key => $value) {
                $collectionAddresses[$key] = \PPLShipping\Serializer::getInstance()->denormalize($value, \PPLShipping\Model\Model\CollectionAddressModel::class);
            }

            if ($collectionAddresses) {
                $collectionAddress = array_filter($collectionAddresses, function (\PPLShipping\Model\Model\CollectionAddressModel $model) {
                    return $model->getCode() === "PICK";
                });
                if ($collectionAddress) {
                    $address = reset($collectionAddress);
                    $address = \PPLShipping\Serializer::getInstance()->normalize($address);
                    Configuration::updateGlobalValue("PPLCollectionAddress", json_encode($address));
                    return $address;
                }
            }
        }
    }
    catch (\Exception $exception)
    {
        return null;
    }
    return $address;
});