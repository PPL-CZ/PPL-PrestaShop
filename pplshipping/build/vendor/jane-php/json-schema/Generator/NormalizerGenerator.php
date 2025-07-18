<?php

namespace PPLShipping\Jane\Component\JsonSchema\Generator;

use PPLShipping\Jane\Component\JsonSchema\Generator\Context\Context;
use PPLShipping\Jane\Component\JsonSchema\Generator\Normalizer\DenormalizerGenerator;
use PPLShipping\Jane\Component\JsonSchema\Generator\Normalizer\JaneObjectNormalizerGenerator;
use PPLShipping\Jane\Component\JsonSchema\Generator\Normalizer\NormalizerGenerator as NormalizerGeneratorTrait;
use PPLShipping\Jane\Component\JsonSchema\Registry\Schema;
use PPLShipping\PhpParser\Node\Expr;
use PPLShipping\PhpParser\Node\Name;
use PPLShipping\PhpParser\Node\NullableType;
use PPLShipping\PhpParser\Node\Param;
use PPLShipping\PhpParser\Node\Scalar;
use PPLShipping\PhpParser\Node\Stmt;
use PPLShipping\PhpParser\Parser;
class NormalizerGenerator implements GeneratorInterface
{
    use DenormalizerGenerator;
    use JaneObjectNormalizerGenerator;
    use NormalizerGeneratorTrait;
    public const FILE_TYPE_NORMALIZER = 'normalizer';
    /**
     * @var Naming The naming service
     */
    protected $naming;
    /**
     * @var Parser PHP Parser
     */
    protected $parser;
    /**
     * @var bool Whether to generate the JSON Reference system
     */
    protected $useReference;
    /**
     * @var bool|null Whether to use the CacheableSupportsMethodInterface interface, for >sf 4.1
     */
    protected $useCacheableSupportsMethod;
    /**
     * @var bool Whether to set property to null when object contains null value for it when property is nullable
     */
    protected $skipNullValues;
    /**
     * @var bool if we handle required fields or not during Normalizer generation
     */
    protected $skipRequiedFields;
    /**
     * @var bool if we run validation or not during normalization/denormalization
     */
    protected $validation;
    /**
     * @param bool $useReference               Whether to generate the JSON Reference system
     * @param bool $useCacheableSupportsMethod Whether to use the CacheableSupportsMethodInterface interface, for >sf 4.1
     * @param bool $skipNullValues             Skip null values or not
     */
    public function __construct(Naming $naming, Parser $parser, bool $useReference = \true, bool $useCacheableSupportsMethod = null, bool $skipNullValues = \true, bool $skipRequiedFields = \false, bool $validation = \false)
    {
        $this->naming = $naming;
        $this->parser = $parser;
        $this->useReference = $useReference;
        $this->useCacheableSupportsMethod = $this->canUseCacheableSupportsMethod($useCacheableSupportsMethod);
        $this->skipNullValues = $skipNullValues;
        $this->skipRequiedFields = $skipRequiedFields;
        $this->validation = $validation;
    }
    /**
     * The naming service.
     */
    protected function getNaming() : Naming
    {
        return $this->naming;
    }
    /**
     * {@inheritdoc}
     */
    public function generate(Schema $schema, string $className, Context $context) : void
    {
        $normalizers = [];
        foreach ($schema->getClasses() as $class) {
            $modelFqdn = $schema->getNamespace() . '\\Model\\' . $class->getName();
            $methods = [];
            $methods[] = $this->createSupportsDenormalizationMethod($modelFqdn);
            $methods[] = $this->createSupportsNormalizationMethod($modelFqdn);
            $methods[] = $this->createDenormalizeMethod($modelFqdn, $context, $class);
            $methods[] = $this->createNormalizeMethod($modelFqdn, $context, $class, $this->skipNullValues, $this->skipRequiedFields);
            $methods[] = $this->createGetSupportedTypesMethod($modelFqdn, $this->useCacheableSupportsMethod);
            if ($this->useCacheableSupportsMethod) {
                $methods[] = $this->createHasCacheableSupportsMethod();
            }
            $normalizerClass = $this->createNormalizerClass($class->getName() . 'Normalizer', $methods, $this->useCacheableSupportsMethod);
            $useStmts = [new Stmt\Use_([new Stmt\UseUse(new Name('PPLShipping\\Jane\\Component\\JsonSchemaRuntime\\Reference'))]), new Stmt\Use_([new Stmt\UseUse(new Name($this->naming->getRuntimeClassFQCN($schema->getNamespace(), ['Normalizer'], 'CheckArray')))]), new Stmt\Use_([new Stmt\UseUse(new Name($this->naming->getRuntimeClassFQCN($schema->getNamespace(), ['Normalizer'], 'ValidatorTrait')))]), new Stmt\Use_([new Stmt\UseUse(new Name('PPLShipping\\Symfony\\Component\\Serializer\\Exception\\InvalidArgumentException'))]), new Stmt\Use_([new Stmt\UseUse(new Name('PPLShipping\\Symfony\\Component\\Serializer\\Normalizer\\DenormalizerAwareInterface'))]), new Stmt\Use_([new Stmt\UseUse(new Name('PPLShipping\\Symfony\\Component\\Serializer\\Normalizer\\DenormalizerAwareTrait'))]), new Stmt\Use_([new Stmt\UseUse(new Name('PPLShipping\\Symfony\\Component\\Serializer\\Normalizer\\DenormalizerInterface'))]), new Stmt\Use_([new Stmt\UseUse(new Name('PPLShipping\\Symfony\\Component\\Serializer\\Normalizer\\NormalizerAwareInterface'))]), new Stmt\Use_([new Stmt\UseUse(new Name('PPLShipping\\Symfony\\Component\\Serializer\\Normalizer\\NormalizerAwareTrait'))]), new Stmt\Use_([new Stmt\UseUse(new Name('PPLShipping\\Symfony\\Component\\Serializer\\Normalizer\\NormalizerInterface'))])];
            if ($this->useCacheableSupportsMethod) {
                $useStmts[] = new Stmt\Use_([new Stmt\UseUse(new Name('PPLShipping\\Symfony\\Component\\Serializer\\Normalizer\\CacheableSupportsMethodInterface'))]);
            }
            $useStmts[] = $normalizerClass;
            $namespace = new Stmt\Namespace_(new Name($schema->getNamespace() . '\\Normalizer'), $useStmts);
            $normalizers[$modelFqdn] = $schema->getNamespace() . '\\Normalizer\\' . $normalizerClass->name;
            $schema->addFile(new File($schema->getDirectory() . '/Normalizer/' . $normalizerClass->name . '.php', $namespace, self::FILE_TYPE_NORMALIZER));
        }
        $schema->addFile(new File($schema->getDirectory() . '/Normalizer/JaneObjectNormalizer.php', new Stmt\Namespace_(new Name($schema->getNamespace() . '\\Normalizer'), $this->createJaneObjectNormalizerClass($schema, $normalizers)), self::FILE_TYPE_NORMALIZER));
    }
    protected function canUseCacheableSupportsMethod(?bool $useCacheableSupportsMethod) : bool
    {
        return \true === $useCacheableSupportsMethod || null === $useCacheableSupportsMethod && \class_exists('PPLShipping\\Symfony\\Component\\Serializer\\Normalizer\\CacheableSupportsMethodInterface');
    }
    protected function createJaneObjectNormalizerClass(Schema $schema, array $normalizers) : array
    {
        if ($this->useReference) {
            $normalizers['\\Jane\\Component\\JsonSchemaRuntime\\Reference'] = '\\' . $this->naming->getRuntimeClassFQCN($schema->getNamespace(), ['Normalizer'], 'ReferenceNormalizer');
        }
        $properties = [];
        $propertyName = $this->getNaming()->getPropertyName('normalizers');
        $propertyStmt = new Stmt\PropertyProperty($propertyName);
        $propertyStmt->default = $this->parser->parse('<?php ' . \var_export($normalizers, \true) . ';')[0]->expr;
        $properties[] = $propertyStmt;
        $propertyStmt = new Stmt\PropertyProperty('normalizersCache');
        $propertyStmt->default = new Expr\Array_();
        $properties[] = $propertyStmt;
        $methods = [];
        $methods[] = new Stmt\Property(Stmt\Class_::MODIFIER_PROTECTED, $properties);
        $methods[] = $this->createBaseNormalizerSupportsDenormalizationMethod();
        $methods[] = $this->createBaseNormalizerSupportsNormalizationMethod();
        $methods[] = $this->createBaseNormalizerNormalizeMethod();
        $methods[] = $this->createBaseNormalizerDenormalizeMethod();
        $methods[] = $this->createBaseNormalizerGetNormalizer();
        $methods[] = $this->createBaseNormalizerInitNormalizerMethod();
        $methods[] = $this->createProxyGetSupportedTypesMethod(\array_keys($normalizers));
        if ($this->useCacheableSupportsMethod) {
            $methods[] = $this->createHasCacheableSupportsMethod();
        }
        $normalizerClass = $this->createNormalizerClass('JaneObjectNormalizer', $methods, $this->useCacheableSupportsMethod);
        $useStmts = [new Stmt\Use_([new Stmt\UseUse(new Name($this->naming->getRuntimeClassFQCN($schema->getNamespace(), ['Normalizer'], 'CheckArray')))]), new Stmt\Use_([new Stmt\UseUse(new Name($this->naming->getRuntimeClassFQCN($schema->getNamespace(), ['Normalizer'], 'ValidatorTrait')))]), new Stmt\Use_([new Stmt\UseUse(new Name('PPLShipping\\Symfony\\Component\\Serializer\\Normalizer\\DenormalizerAwareInterface'))]), new Stmt\Use_([new Stmt\UseUse(new Name('PPLShipping\\Symfony\\Component\\Serializer\\Normalizer\\DenormalizerAwareTrait'))]), new Stmt\Use_([new Stmt\UseUse(new Name('PPLShipping\\Symfony\\Component\\Serializer\\Normalizer\\DenormalizerInterface'))]), new Stmt\Use_([new Stmt\UseUse(new Name('PPLShipping\\Symfony\\Component\\Serializer\\Normalizer\\NormalizerAwareInterface'))]), new Stmt\Use_([new Stmt\UseUse(new Name('PPLShipping\\Symfony\\Component\\Serializer\\Normalizer\\NormalizerAwareTrait'))]), new Stmt\Use_([new Stmt\UseUse(new Name('PPLShipping\\Symfony\\Component\\Serializer\\Normalizer\\NormalizerInterface'))])];
        if ($this->useCacheableSupportsMethod) {
            $useStmts[] = new Stmt\Use_([new Stmt\UseUse(new Name('PPLShipping\\Symfony\\Component\\Serializer\\Normalizer\\CacheableSupportsMethodInterface'))]);
        }
        return \array_merge($useStmts, [$normalizerClass]);
    }
    /**
     * Create method to return the supported type.
     *
     * @param string $modelFqdn Fully Qualified name of the model class denormalized
     *
     * @return Stmt\ClassMethod
     */
    protected function createGetSupportedTypesMethod(string $modelFqdn, bool $useCacheableSupportsMethod = \false)
    {
        return new Stmt\ClassMethod('getSupportedTypes', ['type' => Stmt\Class_::MODIFIER_PUBLIC, 'returnType' => 'array', 'params' => [new Param(new Expr\Variable('format'), new Expr\ConstFetch(new Name('null')), new NullableType('string'))], 'stmts' => [new Stmt\Return_(new Expr\Array_([new Expr\ArrayItem(new Expr\ConstFetch(new Name($useCacheableSupportsMethod ? 'true' : 'false')), new Scalar\String_($modelFqdn))]))]]);
    }
    /**
     * Create method to return the supported type.
     *
     * @param string[] $modelsFqdn Fully Qualified name of the models class denormalized
     *
     * @return Stmt\ClassMethod
     */
    protected function createProxyGetSupportedTypesMethod(array $modelsFqdn)
    {
        $arrayItems = [];
        foreach ($modelsFqdn as $modelFqdn) {
            $arrayItems[] = new Expr\ArrayItem(
                new Expr\ConstFetch(new Name('false')),
                // we don't want proxy Normalizer to be cached, never
                new Scalar\String_($modelFqdn)
            );
        }
        return new Stmt\ClassMethod('getSupportedTypes', ['type' => Stmt\Class_::MODIFIER_PUBLIC, 'returnType' => 'array', 'params' => [new Param(new Expr\Variable('format'), new Expr\ConstFetch(new Name('null')), new NullableType('string'))], 'stmts' => [new Stmt\Return_(new Expr\Array_($arrayItems))]]);
    }
}
