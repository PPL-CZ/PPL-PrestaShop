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
class OpenApiNormalizer implements DenormalizerInterface, NormalizerInterface, DenormalizerAwareInterface, NormalizerAwareInterface
{
    use DenormalizerAwareTrait;
    use NormalizerAwareTrait;
    use CheckArray;
    use ValidatorTrait;
    public function supportsDenormalization($data, $type, $format = null, $context = []) : bool
    {
        return $type === 'Jane\\Component\\OpenApi3\\JsonSchema\\Model\\OpenApi';
    }
    public function supportsNormalization($data, $format = null, $context = []) : bool
    {
        return $data instanceof \PPLShipping\Jane\Component\OpenApi3\JsonSchema\Model\OpenApi;
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
        $object = new \PPLShipping\Jane\Component\OpenApi3\JsonSchema\Model\OpenApi();
        if (null === $data || \false === \is_array($data)) {
            return $object;
        }
        if (\array_key_exists('openapi', $data) && $data['openapi'] !== null) {
            $object->setOpenapi($data['openapi']);
            unset($data['openapi']);
        } elseif (\array_key_exists('openapi', $data) && $data['openapi'] === null) {
            $object->setOpenapi(null);
        }
        if (\array_key_exists('info', $data) && $data['info'] !== null) {
            $object->setInfo($this->denormalizer->denormalize($data['info'], 'PPLShipping\\Jane\\Component\\OpenApi3\\JsonSchema\\Model\\Info', 'json', $context));
            unset($data['info']);
        } elseif (\array_key_exists('info', $data) && $data['info'] === null) {
            $object->setInfo(null);
        }
        if (\array_key_exists('externalDocs', $data) && $data['externalDocs'] !== null) {
            $object->setExternalDocs($this->denormalizer->denormalize($data['externalDocs'], 'PPLShipping\\Jane\\Component\\OpenApi3\\JsonSchema\\Model\\ExternalDocumentation', 'json', $context));
            unset($data['externalDocs']);
        } elseif (\array_key_exists('externalDocs', $data) && $data['externalDocs'] === null) {
            $object->setExternalDocs(null);
        }
        if (\array_key_exists('servers', $data) && $data['servers'] !== null) {
            $values = array();
            foreach ($data['servers'] as $value) {
                $values[] = $this->denormalizer->denormalize($value, 'PPLShipping\\Jane\\Component\\OpenApi3\\JsonSchema\\Model\\Server', 'json', $context);
            }
            $object->setServers($values);
            unset($data['servers']);
        } elseif (\array_key_exists('servers', $data) && $data['servers'] === null) {
            $object->setServers(null);
        }
        if (\array_key_exists('security', $data) && $data['security'] !== null) {
            $values_1 = array();
            foreach ($data['security'] as $value_1) {
                $values_2 = new \ArrayObject(array(), \ArrayObject::ARRAY_AS_PROPS);
                foreach ($value_1 as $key => $value_2) {
                    $values_3 = array();
                    foreach ($value_2 as $value_3) {
                        $values_3[] = $value_3;
                    }
                    $values_2[$key] = $values_3;
                }
                $values_1[] = $values_2;
            }
            $object->setSecurity($values_1);
            unset($data['security']);
        } elseif (\array_key_exists('security', $data) && $data['security'] === null) {
            $object->setSecurity(null);
        }
        if (\array_key_exists('tags', $data) && $data['tags'] !== null) {
            $values_4 = array();
            foreach ($data['tags'] as $value_4) {
                $values_4[] = $this->denormalizer->denormalize($value_4, 'PPLShipping\\Jane\\Component\\OpenApi3\\JsonSchema\\Model\\Tag', 'json', $context);
            }
            $object->setTags($values_4);
            unset($data['tags']);
        } elseif (\array_key_exists('tags', $data) && $data['tags'] === null) {
            $object->setTags(null);
        }
        if (\array_key_exists('paths', $data) && $data['paths'] !== null) {
            $values_5 = new \ArrayObject(array(), \ArrayObject::ARRAY_AS_PROPS);
            foreach ($data['paths'] as $key_1 => $value_5) {
                if (\preg_match('/^\\//', (string) $key_1) && \is_array($value_5)) {
                    $values_5[$key_1] = $this->denormalizer->denormalize($value_5, 'PPLShipping\\Jane\\Component\\OpenApi3\\JsonSchema\\Model\\PathItem', 'json', $context);
                    continue;
                }
                if (\preg_match('/^x-/', (string) $key_1) && isset($value_5)) {
                    $values_5[$key_1] = $value_5;
                    continue;
                }
            }
            $object->setPaths($values_5);
            unset($data['paths']);
        } elseif (\array_key_exists('paths', $data) && $data['paths'] === null) {
            $object->setPaths(null);
        }
        if (\array_key_exists('components', $data) && $data['components'] !== null) {
            $object->setComponents($this->denormalizer->denormalize($data['components'], 'PPLShipping\\Jane\\Component\\OpenApi3\\JsonSchema\\Model\\Components', 'json', $context));
            unset($data['components']);
        } elseif (\array_key_exists('components', $data) && $data['components'] === null) {
            $object->setComponents(null);
        }
        foreach ($data as $key_2 => $value_6) {
            if (\preg_match('/^x-/', (string) $key_2)) {
                $object[$key_2] = $value_6;
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
        $data['openapi'] = $object->getOpenapi();
        $data['info'] = $this->normalizer->normalize($object->getInfo(), 'json', $context);
        if (null !== $object->getExternalDocs()) {
            $data['externalDocs'] = $this->normalizer->normalize($object->getExternalDocs(), 'json', $context);
        }
        if (null !== $object->getServers()) {
            $values = array();
            foreach ($object->getServers() as $value) {
                $values[] = $this->normalizer->normalize($value, 'json', $context);
            }
            $data['servers'] = $values;
        }
        if (null !== $object->getSecurity()) {
            $values_1 = array();
            foreach ($object->getSecurity() as $value_1) {
                $values_2 = array();
                foreach ($value_1 as $key => $value_2) {
                    $values_3 = array();
                    foreach ($value_2 as $value_3) {
                        $values_3[] = $value_3;
                    }
                    $values_2[$key] = $values_3;
                }
                $values_1[] = $values_2;
            }
            $data['security'] = $values_1;
        }
        if (null !== $object->getTags()) {
            $values_4 = array();
            foreach ($object->getTags() as $value_4) {
                $values_4[] = $this->normalizer->normalize($value_4, 'json', $context);
            }
            $data['tags'] = $values_4;
        }
        $values_5 = array();
        foreach ($object->getPaths() as $key_1 => $value_5) {
            if (\preg_match('/^\\//', (string) $key_1) && \is_object($value_5)) {
                $values_5[$key_1] = $this->normalizer->normalize($value_5, 'json', $context);
                continue;
            }
            if (\preg_match('/^x-/', (string) $key_1) && !\is_null($value_5)) {
                $values_5[$key_1] = $value_5;
                continue;
            }
        }
        $data['paths'] = $values_5;
        if (null !== $object->getComponents()) {
            $data['components'] = $this->normalizer->normalize($object->getComponents(), 'json', $context);
        }
        foreach ($object as $key_2 => $value_6) {
            if (\preg_match('/^x-/', (string) $key_2)) {
                $data[$key_2] = $value_6;
            }
        }
        return $data;
    }
}
