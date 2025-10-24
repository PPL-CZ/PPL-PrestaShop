<?php
namespace PPLShipping\ModelNormalizer;

use PPLShipping\Model\Model\WpErrorModel;
use PPLShipping\Errors;
use PPLShipping\Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

class WpErrorModelDenormalizer implements DenormalizerInterface
{


    public function denormalize($data, string $type, ?string $format = null, array $context = [])
    {
        /**
         * @var WP_Error $data
         */
        $output = [];
        foreach ($data->errors as $key =>$values)
        {
            $errorModel = new WpErrorModel();
            $errorModel->setKey($key);
            $errorModel->setValues($values);
            $output[] = $errorModel;
        }
        return $output;
    }

    public function supportsDenormalization($data, string $type, ?string $format = null)
    {
        return $data instanceof Errors && $type === WpErrorModel::class. "[]";

    }
}