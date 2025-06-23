<?php
namespace PPLShipping\ModelNormalizer;

use PPLShipping\Model\Model\ShopGroupModel;
use PPLShipping\Model\Model\ShopModel;
use PPLShipping\Serializer;
use PPLShipping\Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

class ShopModelDenormalizer implements DenormalizerInterface {

    public function denormalize($data, string $type, ?string $format = null, array $context = [])
    {
        if ($data instanceof \ShopGroup) {
            $shopGroup = new ShopGroupModel();
            $shopGroup->setId($data->id);
            $shopGroup->setName($data->name);
            $shops = \Shop::getShops(true, $shopGroup->getId());

            $shopGroup->setShops(array_map(function($shop) {
                $shop = new \Shop($shop['id_shop']);
                return Serializer::getInstance()->denormalize($shop, ShopModel::class);
            }, $shops));
            return $shopGroup;
        } else if ($data instanceof \Shop) {
            $shop = new ShopModel();
            $shop->setId($data->id);
            $shop->setName($data->name);
            return $shop;
        }
    }

    public function supportsDenormalization($data, string $type, ?string $format = null)
    {
        return ($data instanceof \Shop && $type === ShopModel::class
            || $data instanceof \ShopGroup && $type === ShopGroupModel::class);
    }
}