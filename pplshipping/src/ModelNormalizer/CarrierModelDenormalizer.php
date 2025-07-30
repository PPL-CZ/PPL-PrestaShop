<?php
namespace PPLShipping\ModelNormalizer;

use PPLShipping\Model\Model\PrestaCarrierModel;
use PPLShipping\Model\Model\ShopModel;
use PPLShipping\Serializer;
use PPLShipping\Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

class CarrierModelDenormalizer  implements DenormalizerInterface
{

    public function denormalize($data, string $type, ?string $format = null, array $context = [])
    {
        if ($data instanceof \Carrier) {
            $carrier = new PrestaCarrierModel();
            $carrier->setId($data->id_reference);
            $carrier->setName($data->name);
            $carrier->setModule($data->external_module_name);
            $code = \Configuration::getGlobalValue("PPLCarrier{$data->id_reference}") ?: null;
            $carrier->setService($code);
            $carrier->setActive($data->active);
            $sql = 'SELECT id_shop FROM ' . _DB_PREFIX_ . 'carrier_shop WHERE id_carrier = ' . ((int)$data->id);
            $lists = \Db::getInstance()->executeS($sql);
            $shops = [];
            foreach($lists as $item) {
                $shop = new \Shop($item["id_shop"]);
                if (!$shop->id || $shop->deleted)
                    continue;
                $shops[] = Serializer::getInstance()->denormalize($shop, ShopModel::class);
            }
            $carrier->setShops($shops);
            return $carrier;
        }
    }

    public function supportsDenormalization($data, string $type, ?string $format = null)
    {
        return $data instanceof \Carrier && $type === PrestaCarrierModel::class;
    }
}