<?php

namespace PPLShipping\Jane\Component\OpenApi3\JsonSchema\Normalizer;

use PPLShipping\Jane\Component\JsonSchemaRuntime\Reference;
use PPLShipping\Jane\Component\OpenApi3\JsonSchema\Runtime\Normalizer\CheckArray;
use PPLShipping\Jane\Component\OpenApi3\JsonSchema\Runtime\Normalizer\ValidatorTrait;
use PPLShipping\Symfony\Component\Serializer\Exception\InvalidArgumentException;
use PPLShipping\Symfony\Component\Serializer\Normalizer\DenormalizerAwareInterface;
use PPLShipping\Symfony\Component\Serializer\Normalizer\DenormalizerAwareTrait;
use PPLShipping\Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use PPLShipping\Symfony\Component\Serializer\Normalizer\NormalizerAwareInterface;
use PPLShipping\Symfony\Component\Serializer\Normalizer\NormalizerAwareTrait;
use PPLShipping\Symfony\Component\Serializer\Normalizer\NormalizerInterface;
class XMLNormalizer implements DenormalizerInterface, NormalizerInterface, DenormalizerAwareInterface, NormalizerAwareInterface
{
    use DenormalizerAwareTrait;
    use NormalizerAwareTrait;
    use CheckArray;
    use ValidatorTrait;
    public function supportsDenormalization($data, $type, $format = null, $context = []) : bool
    {
        return $type === 'Jane\\Component\\OpenApi3\\JsonSchema\\Model\\XML';
    }
    public function supportsNormalization($data, $format = null, $context = []) : bool
    {
        return $data instanceof \PPLShipping\Jane\Component\OpenApi3\JsonSchema\Model\XML;
    }
    /**
     * @return mixed
     */
    public function denormalize($data, $class, $format = null, array $context = array())
    {
        if (isset($data['$ref'])) {
            return new Reference($data['$ref'], $context['document-origin']);
        }
        if (isset($data['$recursiveRef'])) {
            return new Reference($data['$recursiveRef'], $context['document-origin']);
        }
        $object = new \PPLShipping\Jane\Component\OpenApi3\JsonSchema\Model\XML();
        if (null === $data || \false === \is_array($data)) {
            return $object;
        }
        if (\array_key_exists('name', $data) && $data['name'] !== null) {
            $object->setName($data['name']);
            unset($data['name']);
        } elseif (\array_key_exists('name', $data) && $data['name'] === null) {
            $object->setName(null);
        }
        if (\array_key_exists('namespace', $data) && $data['namespace'] !== null) {
            $object->setNamespace($data['namespace']);
            unset($data['namespace']);
        } elseif (\array_key_exists('namespace', $data) && $data['namespace'] === null) {
            $object->setNamespace(null);
        }
        if (\array_key_exists('prefix', $data) && $data['prefix'] !== null) {
            $object->setPrefix($data['prefix']);
            unset($data['prefix']);
        } elseif (\array_key_exists('prefix', $data) && $data['prefix'] === null) {
            $object->setPrefix(null);
        }
        if (\array_key_exists('attribute', $data) && $data['attribute'] !== null) {
            $object->setAttribute($data['attribute']);
            unset($data['attribute']);
        } elseif (\array_key_exists('attribute', $data) && $data['attribute'] === null) {
            $object->setAttribute(null);
        }
        if (\array_key_exists('wrapped', $data) && $data['wrapped'] !== null) {
            $object->setWrapped($data['wrapped']);
            unset($data['wrapped']);
        } elseif (\array_key_exists('wrapped', $data) && $data['wrapped'] === null) {
            $object->setWrapped(null);
        }
        foreach ($data as $key => $value) {
            if (\preg_match('/^x-/', (string) $key)) {
                $object[$key] = $value;
            }
        }
        return $object;
    }
    /**
     * @return array|string|int|float|bool|\ArrayObject|null
     */
    public function normalize($object, $format = null, array $context = array())
    {
        $data = array();
        if (null !== $object->getName()) {
            $data['name'] = $object->getName();
        }
        if (null !== $object->getNamespace()) {
            $data['namespace'] = $object->getNamespace();
        }
        if (null !== $object->getPrefix()) {
            $data['prefix'] = $object->getPrefix();
        }
        if (null !== $object->getAttribute()) {
            $data['attribute'] = $object->getAttribute();
        }
        if (null !== $object->getWrapped()) {
            $data['wrapped'] = $object->getWrapped();
        }
        foreach ($object as $key => $value) {
            if (\preg_match('/^x-/', (string) $key)) {
                $data[$key] = $value;
            }
        }
        return $data;
    }
}
