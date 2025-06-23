<?php

namespace PPLShipping\Jane\Component\OpenApi3\Generator\RequestBodyContent;

use PPLShipping\Jane\Component\JsonSchema\Generator\Context\Context;
use PPLShipping\Jane\Component\OpenApi3\JsonSchema\Model\MediaType;
use PPLShipping\PhpParser\Node\Expr;
use PPLShipping\PhpParser\Node\Scalar;
use PPLShipping\PhpParser\Node\Stmt;
class DefaultBodyContentGenerator extends AbstractBodyContentGenerator
{
    /**
     * {@inheritdoc}
     */
    public function getSerializeStatements(MediaType $content, string $contentType, string $reference, Context $context) : array
    {
        return [new Stmt\Return_(new Expr\Array_([new Expr\Array_([new Expr\ArrayItem(new Expr\Array_([new Expr\ArrayItem(new Scalar\String_($contentType))]), new Scalar\String_('Content-Type'))]), new Expr\PropertyFetch(new Expr\Variable('this'), 'body')]))];
    }
}
