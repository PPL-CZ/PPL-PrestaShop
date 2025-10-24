<?php

namespace PPLShipping;


use PPLShipping\CPLNormalizer\CPLBatchPackageDenormalizer;
use PPLShipping\CPLNormalizer\CPLBatchAddressDenormalizer;
use PPLShipping\CPLNormalizer\CPLBatchCreateShipmentsDenormalizer;
use PPLShipping\CPLNormalizer\CPLBatchShipmentDenormalizer;
use PPLShipping\Model\Model\CollectionAddressModel;
use PPLShipping\Model\Normalizer\CategoryModelNormalizer;
use PPLShipping\Model\Normalizer\CollectionModelNormalizer;
use PPLShipping\Model\Normalizer\CountryModelNormalizer;
use PPLShipping\Model\Normalizer\CreateShipmentLabelBatchModelNormalizer;
use PPLShipping\Model\Normalizer\CurrencyModelNormalizer;
use PPLShipping\Model\Normalizer\JaneObjectNormalizer;
use PPLShipping\Model\Normalizer\ShipmentCartModelNormalizer;
use PPLShipping\ModelNormalizer\AddressModelDenormalizer;
use PPLShipping\ModelNormalizer\BatchModelDenormalizer;
use PPLShipping\ModelNormalizer\CarrierModelDenormalizer;
use PPLShipping\ModelNormalizer\CartModelDenormalizer;
use PPLShipping\ModelNormalizer\ErrorLogModelDenormalizer;
use PPLShipping\ModelNormalizer\ParcelPlacesModelDenormalizer;
use PPLShipping\ModelNormalizer\RulesDenormalizer;
use PPLShipping\ModelNormalizer\CollectionModelDenormalizer;
use PPLShipping\ModelNormalizer\PackageModelDenormalizer;
use PPLShipping\ModelNormalizer\ParcelAddressModelDenormalizer;
use PPLShipping\ModelNormalizer\ShipmentDataDenormalizer;
use PPLShipping\ModelNormalizer\ShipmentWithAdditionalModelDenormalizer;
use PPLShipping\ModelNormalizer\ShopModelDenormalizer;
use PPLShipping\ModelNormalizer\WpErrorModelDenormalizer;

class Serializer extends Symfony\Component\Serializer\Serializer
{
    public function __construct(array $normalizer = [], array $encoders = [])
    {
        parent::__construct([
            new ShopModelDenormalizer(),
            new AddressModelDenormalizer(),
            new CarrierModelDenormalizer(),
            new ParcelAddressModelDenormalizer(),
            new CartModelDenormalizer(),
            new PackageModelDenormalizer(),
            new ShipmentDataDenormalizer(),
            new CollectionModelDenormalizer(),
            new RulesDenormalizer(),
            new ErrorLogModelDenormalizer(),
            new ParcelPlacesModelDenormalizer(),

            new CPLBatchPackageDenormalizer(),
            new CPLBatchAddressDenormalizer(),
            new CPLBatchCreateShipmentsDenormalizer(),
            new CPLBatchShipmentDenormalizer(),

            new ShipmentWithAdditionalModelDenormalizer(),
            new WpErrorModelDenormalizer(),
            new BatchModelDenormalizer(),

            new JaneObjectNormalizer(),

        ]);
    }


    protected static $instance;

    public static function getInstance() {
        return self::$instance ?: (self::$instance = new self());
    }
}

