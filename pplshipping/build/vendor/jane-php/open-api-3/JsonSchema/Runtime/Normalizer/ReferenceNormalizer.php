<?php

namespace PPLShipping\Jane\Component\OpenApi3\JsonSchema\Runtime\Normalizer;

use PPLShipping\Jane\Component\JsonSchemaRuntime\Reference;
use PPLShipping\Symfony\Component\Serializer\Normalizer\NormalizerInterface;
class ReferenceNormalizer implements NormalizerInterface
{
    /**
     * {@inheritdoc}
     */
    public function normalize($object, $format = null, array $context = [])
    {
        $ref = [];
        $ref['$ref'] = (string) $object->getReferenceUri();
        return $ref;
    }
    /**
     * {@inheritdoc}
     */
    public function supportsNormalization($data, $format = null, $context = []) : bool
    {
        return $data instanceof Reference;
    }
}
