<?php

namespace PPLShipping\Jane\Component\OpenApiCommon\Generator\Client;

use PPLShipping\Http\Client\Common\PluginClient;
use PPLShipping\Http\Discovery\Psr18ClientDiscovery;
use PPLShipping\Jane\Component\JsonSchema\Generator\Context\Context;
use PPLShipping\Jane\Component\OpenApi3\JsonSchema\Model\OpenApi;
use PPLShipping\PhpParser\Node;
use PPLShipping\PhpParser\Node\Expr;
use PPLShipping\PhpParser\Node\Name;
use PPLShipping\PhpParser\Node\Stmt;
trait HttpClientCreateGenerator
{
    protected function getHttpClientCreateExpr(Context $context) : array
    {
        /** @var OpenApi $openApi */
        $openApi = $context->getCurrentSchema()->getParsed();
        $statements = [new Stmt\Expression(new Expr\Assign(new Expr\Variable('httpClient'), new Expr\StaticCall(new Name\FullyQualified(Psr18ClientDiscovery::class), 'find')))];
        $needsServerPlugins = $this->needsServerPlugins($openApi);
        $statements[] = new Stmt\Expression(new Expr\Assign(new Expr\Variable('plugins'), new Expr\Array_()));
        if ($needsServerPlugins) {
            $statements = \array_merge($statements, $this->getServerPluginsStatements($openApi));
        }
        $statements[] = new Stmt\If_(new Expr\BinaryOp\Greater(new Expr\FuncCall(new Name('count'), [new Node\Arg(new Expr\Variable('additionalPlugins'))]), new Expr\ConstFetch(new Name('0'))), ['stmts' => [new Stmt\Expression(new Expr\Assign(new Expr\Variable('plugins'), new Expr\FuncCall(new Name('array_merge'), [new Node\Arg(new Expr\Variable('plugins')), new Node\Arg(new Expr\Variable('additionalPlugins'))])))]]);
        $statements[] = new Stmt\Expression(new Expr\Assign(new Expr\Variable('httpClient'), new Expr\New_(new Name\FullyQualified(PluginClient::class), [new Node\Arg(new Expr\Variable('httpClient')), new Node\Arg(new Expr\Variable('plugins'))])));
        return $statements;
    }
}
