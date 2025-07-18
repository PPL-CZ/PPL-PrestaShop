<?php

namespace PPLShipping\Jane\Component\OpenApi3\Generator\RequestBodyContent;

use PPLShipping\Http\Message\MultipartStream\MultipartStreamBuilder;
use PPLShipping\Jane\Component\JsonSchema\Generator\Context\Context;
use PPLShipping\Jane\Component\OpenApi3\JsonSchema\Model\MediaType;
use PPLShipping\PhpParser\Node\Arg;
use PPLShipping\PhpParser\Node\Expr;
use PPLShipping\PhpParser\Node\Name;
use PPLShipping\PhpParser\Node\Scalar;
use PPLShipping\PhpParser\Node\Stmt;
class FormBodyContentGenerator extends AbstractBodyContentGenerator
{
    /**
     * {@inheritdoc}
     */
    public function getSerializeStatements(MediaType $content, string $contentType, string $reference, Context $context) : array
    {
        if (\preg_match('/multipart\\/form-data/', $contentType)) {
            return [new Stmt\Expression(new Expr\Assign(new Expr\Variable('bodyBuilder'), new Expr\New_(new Name('\\' . MultipartStreamBuilder::class), [new Arg(new Expr\Variable('streamFactory'))]))), new Stmt\Expression(new Expr\Assign(new Expr\Variable('formParameters'), new Expr\MethodCall(new Expr\Variable('serializer'), 'normalize', [new Arg(new Expr\PropertyFetch(new Expr\Variable('this'), 'body')), new Arg(new Scalar\String_('json'))]))), new Stmt\Foreach_(new Expr\Variable('formParameters'), new Expr\Variable('value'), ['keyVar' => new Expr\Variable('key'), 'stmts' => [new Stmt\Expression(new Expr\Assign(new Expr\Variable('value'), new Expr\Ternary(new Expr\FuncCall(new Name('is_int'), [new Arg(new Expr\Variable('value'))]), new Expr\Cast\String_(new Expr\Variable('value')), new Expr\Variable('value')))), new Stmt\Expression(new Expr\MethodCall(new Expr\Variable('bodyBuilder'), 'addResource', [new Arg(new Expr\Variable('key')), new Arg(new Expr\Variable('value'))]))]]), new Stmt\Return_(new Expr\Array_([new Expr\Array_([new Expr\ArrayItem(new Expr\Array_([new Expr\ArrayItem(new Expr\BinaryOp\Concat(new Scalar\String_('multipart/form-data; boundary="'), new Expr\BinaryOp\Concat(new Expr\MethodCall(new Expr\Variable('bodyBuilder'), 'getBoundary'), new Scalar\String_('"'))))]), new Scalar\String_('Content-Type'))]), new Expr\MethodCall(new Expr\Variable('bodyBuilder'), 'build')]))];
        }
        return [new Stmt\Return_(new Expr\Array_([new Expr\Array_([new Expr\ArrayItem(new Expr\Array_([new Expr\ArrayItem(new Scalar\String_($contentType))]), new Scalar\String_('Content-Type'))]), new Expr\FuncCall(new Name('http_build_query'), [new Arg(new Expr\MethodCall(new Expr\Variable('serializer'), new Name('normalize'), [new Arg(new Expr\PropertyFetch(new Expr\Variable('this'), 'body')), new Arg(new Scalar\String_('json'))]))])]))];
    }
}
