<?php

namespace PPLShipping\Jane\Component\OpenApiCommon\Generator\Client;

use PPLShipping\Http\Discovery\Psr17FactoryDiscovery;
use PPLShipping\Jane\Component\JsonSchema\Generator\Context\Context;
use PPLShipping\Jane\Component\JsonSchema\Generator\Naming;
use PPLShipping\Jane\Component\JsonSchema\Registry\Schema;
use PPLShipping\Jane\Component\JsonSchema\Registry\Schema as BaseSchema;
use PPLShipping\PhpParser\Node;
use PPLShipping\PhpParser\Node\Expr;
use PPLShipping\PhpParser\Node\Name;
use PPLShipping\PhpParser\Node\Scalar;
use PPLShipping\PhpParser\Node\Stmt;
use PPLShipping\Symfony\Component\Serializer\Encoder\JsonDecode;
use PPLShipping\Symfony\Component\Serializer\Encoder\JsonEncode;
use PPLShipping\Symfony\Component\Serializer\Encoder\JsonEncoder;
use PPLShipping\Symfony\Component\Serializer\Serializer;
trait ClientGenerator
{
    protected abstract function getHttpClientCreateExpr(Context $context) : array;
    protected function getSuffix() : string
    {
        return '';
    }
    protected function createResourceClass(Schema $schema, string $name) : Stmt\Class_
    {
        $naming = new Naming();
        return new Stmt\Class_($name, ['extends' => new Node\Name\FullyQualified($naming->getRuntimeClassFQCN($schema->getNamespace(), ['Client'], 'Client'))]);
    }
    protected function getFactoryMethod(BaseSchema $schema, Context $context) : Stmt
    {
        $params = [new Node\Param(new Expr\Variable('httpClient'), new Expr\ConstFetch(new Name('null'))), new Node\Param(new Expr\Variable('additionalPlugins'), new Expr\Array_(), 'array'), new Node\Param(new Expr\Variable('additionalNormalizers'), new Expr\Array_(), 'array')];
        return new Stmt\ClassMethod('create', ['flags' => Stmt\Class_::MODIFIER_STATIC | Stmt\Class_::MODIFIER_PUBLIC, 'params' => $params, 'stmts' => [new Stmt\If_(new Expr\BinaryOp\Identical(new Expr\ConstFetch(new Name('null')), new Expr\Variable('httpClient')), ['stmts' => $this->getHttpClientCreateExpr($context)]), new Stmt\Expression(new Expr\Assign(new Expr\Variable('requestFactory'), new Expr\StaticCall(new Name\FullyQualified(Psr17FactoryDiscovery::class), 'findRequestFactory'))), new Stmt\Expression(new Expr\Assign(new Expr\Variable('streamFactory'), new Expr\StaticCall(new Name\FullyQualified(Psr17FactoryDiscovery::class), 'findStreamFactory'))), new Stmt\Expression(new Expr\Assign(new Expr\Variable('normalizers'), new Expr\Array_([new Expr\ArrayItem(new Expr\New_(new Name('PPLShipping\\Symfony\\Component\\Serializer\\Normalizer\\ArrayDenormalizer'))), new Expr\ArrayItem(new Expr\New_(new Name('\\' . $context->getCurrentSchema()->getNamespace() . '\\Normalizer\\JaneObjectNormalizer')))]))), new Stmt\If_(new Expr\BinaryOp\Greater(new Expr\FuncCall(new Name('count'), [new Node\Arg(new Expr\Variable('additionalNormalizers'))]), new Expr\ConstFetch(new Name('0'))), ['stmts' => [new Stmt\Expression(new Expr\Assign(new Expr\Variable('normalizers'), new Expr\FuncCall(new Name('array_merge'), [new Node\Arg(new Expr\Variable('normalizers')), new Node\Arg(new Expr\Variable('additionalNormalizers'))])))]]), new Stmt\Expression(new Expr\Assign(new Expr\Variable('serializer'), new Expr\New_(new Name\FullyQualified(Serializer::class), [new Node\Arg(new Expr\Variable('normalizers')), new Node\Arg(new Expr\Array_([new Expr\ArrayItem(new Expr\New_(new Name\FullyQualified(JsonEncoder::class), [new Node\Arg(new Expr\New_(new Name\FullyQualified(JsonEncode::class))), new Node\Arg(new Expr\New_(new Name\FullyQualified(JsonDecode::class), [new Node\Arg(new Expr\Array_([new Expr\ArrayItem(new Expr\ConstFetch(new Name('true')), new Scalar\String_('json_decode_associative'))]))]))]))]))]))), new Stmt\Return_(new Expr\New_(new Name('static'), [new Node\Arg(new Expr\Variable('httpClient')), new Node\Arg(new Expr\Variable('requestFactory')), new Node\Arg(new Expr\Variable('serializer')), new Node\Arg(new Expr\Variable('streamFactory'))]))]]);
    }
}
