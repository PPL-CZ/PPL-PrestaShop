<?php

namespace PPLShipping\Jane\Component\OpenApi3\JsonSchema\Normalizer;

use PPLShipping\Jane\Component\OpenApi3\JsonSchema\Runtime\Normalizer\CheckArray;
use PPLShipping\Jane\Component\OpenApi3\JsonSchema\Runtime\Normalizer\ValidatorTrait;
use PPLShipping\Symfony\Component\Serializer\Normalizer\DenormalizerAwareInterface;
use PPLShipping\Symfony\Component\Serializer\Normalizer\DenormalizerAwareTrait;
use PPLShipping\Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use PPLShipping\Symfony\Component\Serializer\Normalizer\NormalizerAwareInterface;
use PPLShipping\Symfony\Component\Serializer\Normalizer\NormalizerAwareTrait;
use PPLShipping\Symfony\Component\Serializer\Normalizer\NormalizerInterface;
class JaneObjectNormalizer implements DenormalizerInterface, NormalizerInterface, DenormalizerAwareInterface, NormalizerAwareInterface
{
    use DenormalizerAwareTrait;
    use NormalizerAwareTrait;
    use CheckArray;
    use ValidatorTrait;
    protected $normalizers = array('PPLShipping\\Jane\\Component\\OpenApi3\\JsonSchema\\Model\\OpenApi' => 'PPLShipping\\Jane\\Component\\OpenApi3\\JsonSchema\\Normalizer\\OpenApiNormalizer', 'PPLShipping\\Jane\\Component\\OpenApi3\\JsonSchema\\Model\\Reference' => 'PPLShipping\\Jane\\Component\\OpenApi3\\JsonSchema\\Normalizer\\ReferenceNormalizer', 'PPLShipping\\Jane\\Component\\OpenApi3\\JsonSchema\\Model\\Info' => 'PPLShipping\\Jane\\Component\\OpenApi3\\JsonSchema\\Normalizer\\InfoNormalizer', 'PPLShipping\\Jane\\Component\\OpenApi3\\JsonSchema\\Model\\Contact' => 'PPLShipping\\Jane\\Component\\OpenApi3\\JsonSchema\\Normalizer\\ContactNormalizer', 'PPLShipping\\Jane\\Component\\OpenApi3\\JsonSchema\\Model\\License' => 'PPLShipping\\Jane\\Component\\OpenApi3\\JsonSchema\\Normalizer\\LicenseNormalizer', 'PPLShipping\\Jane\\Component\\OpenApi3\\JsonSchema\\Model\\Server' => 'PPLShipping\\Jane\\Component\\OpenApi3\\JsonSchema\\Normalizer\\ServerNormalizer', 'PPLShipping\\Jane\\Component\\OpenApi3\\JsonSchema\\Model\\ServerVariable' => 'PPLShipping\\Jane\\Component\\OpenApi3\\JsonSchema\\Normalizer\\ServerVariableNormalizer', 'PPLShipping\\Jane\\Component\\OpenApi3\\JsonSchema\\Model\\Components' => 'PPLShipping\\Jane\\Component\\OpenApi3\\JsonSchema\\Normalizer\\ComponentsNormalizer', 'PPLShipping\\Jane\\Component\\OpenApi3\\JsonSchema\\Model\\Schema' => 'PPLShipping\\Jane\\Component\\OpenApi3\\JsonSchema\\Normalizer\\SchemaNormalizer', 'PPLShipping\\Jane\\Component\\OpenApi3\\JsonSchema\\Model\\Discriminator' => 'PPLShipping\\Jane\\Component\\OpenApi3\\JsonSchema\\Normalizer\\DiscriminatorNormalizer', 'PPLShipping\\Jane\\Component\\OpenApi3\\JsonSchema\\Model\\XML' => 'PPLShipping\\Jane\\Component\\OpenApi3\\JsonSchema\\Normalizer\\XMLNormalizer', 'PPLShipping\\Jane\\Component\\OpenApi3\\JsonSchema\\Model\\Response' => 'PPLShipping\\Jane\\Component\\OpenApi3\\JsonSchema\\Normalizer\\ResponseNormalizer', 'PPLShipping\\Jane\\Component\\OpenApi3\\JsonSchema\\Model\\MediaType' => 'PPLShipping\\Jane\\Component\\OpenApi3\\JsonSchema\\Normalizer\\MediaTypeNormalizer', 'PPLShipping\\Jane\\Component\\OpenApi3\\JsonSchema\\Model\\Example' => 'PPLShipping\\Jane\\Component\\OpenApi3\\JsonSchema\\Normalizer\\ExampleNormalizer', 'PPLShipping\\Jane\\Component\\OpenApi3\\JsonSchema\\Model\\Header' => 'PPLShipping\\Jane\\Component\\OpenApi3\\JsonSchema\\Normalizer\\HeaderNormalizer', 'PPLShipping\\Jane\\Component\\OpenApi3\\JsonSchema\\Model\\PathItem' => 'PPLShipping\\Jane\\Component\\OpenApi3\\JsonSchema\\Normalizer\\PathItemNormalizer', 'PPLShipping\\Jane\\Component\\OpenApi3\\JsonSchema\\Model\\Operation' => 'PPLShipping\\Jane\\Component\\OpenApi3\\JsonSchema\\Normalizer\\OperationNormalizer', 'PPLShipping\\Jane\\Component\\OpenApi3\\JsonSchema\\Model\\Responses' => 'PPLShipping\\Jane\\Component\\OpenApi3\\JsonSchema\\Normalizer\\ResponsesNormalizer', 'PPLShipping\\Jane\\Component\\OpenApi3\\JsonSchema\\Model\\Tag' => 'PPLShipping\\Jane\\Component\\OpenApi3\\JsonSchema\\Normalizer\\TagNormalizer', 'PPLShipping\\Jane\\Component\\OpenApi3\\JsonSchema\\Model\\ExternalDocumentation' => 'PPLShipping\\Jane\\Component\\OpenApi3\\JsonSchema\\Normalizer\\ExternalDocumentationNormalizer', 'PPLShipping\\Jane\\Component\\OpenApi3\\JsonSchema\\Model\\Parameter' => 'PPLShipping\\Jane\\Component\\OpenApi3\\JsonSchema\\Normalizer\\ParameterNormalizer', 'PPLShipping\\Jane\\Component\\OpenApi3\\JsonSchema\\Model\\RequestBody' => 'PPLShipping\\Jane\\Component\\OpenApi3\\JsonSchema\\Normalizer\\RequestBodyNormalizer', 'PPLShipping\\Jane\\Component\\OpenApi3\\JsonSchema\\Model\\APIKeySecurityScheme' => 'PPLShipping\\Jane\\Component\\OpenApi3\\JsonSchema\\Normalizer\\APIKeySecuritySchemeNormalizer', 'PPLShipping\\Jane\\Component\\OpenApi3\\JsonSchema\\Model\\HTTPSecurityScheme' => 'PPLShipping\\Jane\\Component\\OpenApi3\\JsonSchema\\Normalizer\\HTTPSecuritySchemeNormalizer', 'PPLShipping\\Jane\\Component\\OpenApi3\\JsonSchema\\Model\\HTTPSecuritySchemeSub' => 'PPLShipping\\Jane\\Component\\OpenApi3\\JsonSchema\\Normalizer\\HTTPSecuritySchemeSubNormalizer', 'PPLShipping\\Jane\\Component\\OpenApi3\\JsonSchema\\Model\\OAuth2SecurityScheme' => 'PPLShipping\\Jane\\Component\\OpenApi3\\JsonSchema\\Normalizer\\OAuth2SecuritySchemeNormalizer', 'PPLShipping\\Jane\\Component\\OpenApi3\\JsonSchema\\Model\\OpenIdConnectSecurityScheme' => 'PPLShipping\\Jane\\Component\\OpenApi3\\JsonSchema\\Normalizer\\OpenIdConnectSecuritySchemeNormalizer', 'PPLShipping\\Jane\\Component\\OpenApi3\\JsonSchema\\Model\\OAuthFlows' => 'PPLShipping\\Jane\\Component\\OpenApi3\\JsonSchema\\Normalizer\\OAuthFlowsNormalizer', 'PPLShipping\\Jane\\Component\\OpenApi3\\JsonSchema\\Model\\ImplicitOAuthFlow' => 'PPLShipping\\Jane\\Component\\OpenApi3\\JsonSchema\\Normalizer\\ImplicitOAuthFlowNormalizer', 'PPLShipping\\Jane\\Component\\OpenApi3\\JsonSchema\\Model\\PasswordOAuthFlow' => 'PPLShipping\\Jane\\Component\\OpenApi3\\JsonSchema\\Normalizer\\PasswordOAuthFlowNormalizer', 'PPLShipping\\Jane\\Component\\OpenApi3\\JsonSchema\\Model\\ClientCredentialsFlow' => 'PPLShipping\\Jane\\Component\\OpenApi3\\JsonSchema\\Normalizer\\ClientCredentialsFlowNormalizer', 'PPLShipping\\Jane\\Component\\OpenApi3\\JsonSchema\\Model\\AuthorizationCodeOAuthFlow' => 'PPLShipping\\Jane\\Component\\OpenApi3\\JsonSchema\\Normalizer\\AuthorizationCodeOAuthFlowNormalizer', 'PPLShipping\\Jane\\Component\\OpenApi3\\JsonSchema\\Model\\Link' => 'PPLShipping\\Jane\\Component\\OpenApi3\\JsonSchema\\Normalizer\\LinkNormalizer', 'PPLShipping\\Jane\\Component\\OpenApi3\\JsonSchema\\Model\\Encoding' => 'PPLShipping\\Jane\\Component\\OpenApi3\\JsonSchema\\Normalizer\\EncodingNormalizer', 'PPLShipping\\Jane\\Component\\JsonSchemaRuntime\\Reference' => 'PPLShipping\\Jane\\Component\\OpenApi3\\JsonSchema\\Runtime\\Normalizer\\ReferenceNormalizer'), $normalizersCache = array();
    public function supportsDenormalization($data, $type, $format = null, $context = []) : bool
    {
        return \array_key_exists($type, $this->normalizers);
    }
    public function supportsNormalization($data, $format = null, $context = []) : bool
    {
        return \is_object($data) && \array_key_exists(\get_class($data), $this->normalizers);
    }
    /**
     * @return array|string|int|float|bool|\ArrayObject|null
     */
    public function normalize($object, $format = null, array $context = array())
    {
        $normalizerClass = $this->normalizers[\get_class($object)];
        $normalizer = $this->getNormalizer($normalizerClass);
        return $normalizer->normalize($object, $format, $context);
    }
    /**
     * @return mixed
     */
    public function denormalize($data, $class, $format = null, array $context = array())
    {
        $denormalizerClass = $this->normalizers[$class];
        $denormalizer = $this->getNormalizer($denormalizerClass);
        return $denormalizer->denormalize($data, $class, $format, $context);
    }
    private function getNormalizer(string $normalizerClass)
    {
        return $this->normalizersCache[$normalizerClass] ?? $this->initNormalizer($normalizerClass);
    }
    private function initNormalizer(string $normalizerClass)
    {
        $normalizer = new $normalizerClass();
        $normalizer->setNormalizer($this->normalizer);
        $normalizer->setDenormalizer($this->denormalizer);
        $this->normalizersCache[$normalizerClass] = $normalizer;
        return $normalizer;
    }
}
