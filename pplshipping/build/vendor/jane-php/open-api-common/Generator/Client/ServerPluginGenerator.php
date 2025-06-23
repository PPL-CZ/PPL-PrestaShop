<?php

namespace PPLShipping\Jane\Component\OpenApiCommon\Generator\Client;

use PPLShipping\Http\Discovery\Psr17FactoryDiscovery;
use PPLShipping\PhpParser\Node;
use PPLShipping\PhpParser\Node\Expr;
use PPLShipping\PhpParser\Node\Name;
use PPLShipping\PhpParser\Node\Stmt;
trait ServerPluginGenerator
{
    protected abstract function discoverServer($openApi) : array;
    protected function needsServerPlugins($openApi) : bool
    {
        [$baseUri, $_] = $this->discoverServer($openApi);
        return !(empty($baseUri) || $baseUri === '/');
    }
    protected function getServerPluginsStatements($openApi) : array
    {
        [$baseUri, $plugins] = $this->discoverServer($openApi);
        $stmts = [new Stmt\Expression(new Expr\Assign(new Expr\Variable('uri'), new Expr\MethodCall(new Expr\StaticCall(new Name\FullyQualified(Psr17FactoryDiscovery::class), 'findUriFactory'), 'createUri', [new Node\Arg(new Node\Scalar\String_($baseUri))])))];
        foreach ($plugins as $pluginClass) {
            $stmts[] = new Stmt\Expression(new Expr\Assign(new Expr\ArrayDimFetch(new Expr\Variable('plugins')), new Expr\New_(new Name\FullyQualified($pluginClass), [new Node\Arg(new Expr\Variable('uri'))])));
        }
        return $stmts;
    }
}
