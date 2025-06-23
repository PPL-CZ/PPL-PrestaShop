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
class OAuthFlowsNormalizer implements DenormalizerInterface, NormalizerInterface, DenormalizerAwareInterface, NormalizerAwareInterface
{
    use DenormalizerAwareTrait;
    use NormalizerAwareTrait;
    use CheckArray;
    use ValidatorTrait;
    public function supportsDenormalization($data, $type, $format = null, $context = []) : bool
    {
        return $type === 'Jane\\Component\\OpenApi3\\JsonSchema\\Model\\OAuthFlows';
    }
    public function supportsNormalization($data, $format = null, $context = []) : bool
    {
        return $data instanceof \PPLShipping\Jane\Component\OpenApi3\JsonSchema\Model\OAuthFlows;
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
        $object = new \PPLShipping\Jane\Component\OpenApi3\JsonSchema\Model\OAuthFlows();
        if (null === $data || \false === \is_array($data)) {
            return $object;
        }
        if (\array_key_exists('implicit', $data) && $data['implicit'] !== null) {
            $object->setImplicit($this->denormalizer->denormalize($data['implicit'], 'PPLShipping\\Jane\\Component\\OpenApi3\\JsonSchema\\Model\\ImplicitOAuthFlow', 'json', $context));
            unset($data['implicit']);
        } elseif (\array_key_exists('implicit', $data) && $data['implicit'] === null) {
            $object->setImplicit(null);
        }
        if (\array_key_exists('password', $data) && $data['password'] !== null) {
            $object->setPassword($this->denormalizer->denormalize($data['password'], 'PPLShipping\\Jane\\Component\\OpenApi3\\JsonSchema\\Model\\PasswordOAuthFlow', 'json', $context));
            unset($data['password']);
        } elseif (\array_key_exists('password', $data) && $data['password'] === null) {
            $object->setPassword(null);
        }
        if (\array_key_exists('clientCredentials', $data) && $data['clientCredentials'] !== null) {
            $object->setClientCredentials($this->denormalizer->denormalize($data['clientCredentials'], 'PPLShipping\\Jane\\Component\\OpenApi3\\JsonSchema\\Model\\ClientCredentialsFlow', 'json', $context));
            unset($data['clientCredentials']);
        } elseif (\array_key_exists('clientCredentials', $data) && $data['clientCredentials'] === null) {
            $object->setClientCredentials(null);
        }
        if (\array_key_exists('authorizationCode', $data) && $data['authorizationCode'] !== null) {
            $object->setAuthorizationCode($this->denormalizer->denormalize($data['authorizationCode'], 'PPLShipping\\Jane\\Component\\OpenApi3\\JsonSchema\\Model\\AuthorizationCodeOAuthFlow', 'json', $context));
            unset($data['authorizationCode']);
        } elseif (\array_key_exists('authorizationCode', $data) && $data['authorizationCode'] === null) {
            $object->setAuthorizationCode(null);
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
        if (null !== $object->getImplicit()) {
            $data['implicit'] = $this->normalizer->normalize($object->getImplicit(), 'json', $context);
        }
        if (null !== $object->getPassword()) {
            $data['password'] = $this->normalizer->normalize($object->getPassword(), 'json', $context);
        }
        if (null !== $object->getClientCredentials()) {
            $data['clientCredentials'] = $this->normalizer->normalize($object->getClientCredentials(), 'json', $context);
        }
        if (null !== $object->getAuthorizationCode()) {
            $data['authorizationCode'] = $this->normalizer->normalize($object->getAuthorizationCode(), 'json', $context);
        }
        foreach ($object as $key => $value) {
            if (\preg_match('/^x-/', (string) $key)) {
                $data[$key] = $value;
            }
        }
        return $data;
    }
}
