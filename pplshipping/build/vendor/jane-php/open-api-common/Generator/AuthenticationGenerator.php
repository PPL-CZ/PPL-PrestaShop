<?php

namespace PPLShipping\Jane\Component\OpenApiCommon\Generator;

use PPLShipping\Jane\Component\JsonSchema\Generator\Context\Context;
use PPLShipping\Jane\Component\JsonSchema\Generator\File;
use PPLShipping\Jane\Component\JsonSchema\Generator\GeneratorInterface;
use PPLShipping\Jane\Component\JsonSchema\Generator\Naming;
use PPLShipping\Jane\Component\JsonSchema\Registry\Schema as BaseSchema;
use PPLShipping\Jane\Component\OpenApiCommon\Generator\Authentication\AuthenticationGenerator as AuthenticationMethodGenerator;
use PPLShipping\Jane\Component\OpenApiCommon\Generator\Authentication\ClassGenerator;
use PPLShipping\Jane\Component\OpenApiCommon\Generator\Authentication\ConstructGenerator;
use PPLShipping\Jane\Component\OpenApiCommon\Generator\Authentication\GetScopeGenerator;
use PPLShipping\Jane\Component\OpenApiCommon\Registry\Schema;
use PPLShipping\PhpParser\Node\Name;
use PPLShipping\PhpParser\Node\Stmt;
class AuthenticationGenerator implements GeneratorInterface
{
    use AuthenticationMethodGenerator;
    use ClassGenerator;
    use ConstructGenerator;
    use GetScopeGenerator;
    protected const REFERENCE = 'Authentication';
    protected const FILE_TYPE_AUTH = 'auth';
    protected $naming;
    public function __construct()
    {
        $this->naming = new Naming();
    }
    protected function getNaming() : Naming
    {
        return $this->naming;
    }
    public function generate(BaseSchema $schema, string $className, Context $context) : void
    {
        if ($schema instanceof Schema) {
            $baseNamespace = \sprintf('%s\\%s', $schema->getNamespace(), self::REFERENCE);
            $securitySchemes = $schema->getSecuritySchemes();
            foreach ($securitySchemes as $securityScheme) {
                $className = $this->getNaming()->getAuthName($securityScheme->getName());
                $statements = $this->createConstruct($securityScheme);
                $statements[] = $this->createAuthentication($securityScheme);
                $statements[] = $this->createGetScope($securityScheme);
                $authentication = $this->createClass($className, $statements);
                $namespace = new Stmt\Namespace_(new Name($baseNamespace), [$authentication]);
                $schema->addFile(new File(\sprintf('%s/%s/%s.php', $schema->getDirectory(), self::REFERENCE, $className), $namespace, self::FILE_TYPE_AUTH));
            }
        }
    }
}
