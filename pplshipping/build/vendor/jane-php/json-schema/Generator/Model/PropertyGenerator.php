<?php

namespace PPLShipping\Jane\Component\JsonSchema\Generator\Model;

use PPLShipping\Jane\Component\JsonSchema\Generator\Naming;
use PPLShipping\Jane\Component\JsonSchema\Guesser\Guess\Property;
use PPLShipping\Jane\Component\JsonSchema\Guesser\Guess\Type;
use PPLShipping\PhpParser\Comment\Doc;
use PPLShipping\PhpParser\Node\Stmt;
use PPLShipping\PhpParser\Parser;
trait PropertyGenerator
{
    /**
     * The naming service.
     */
    protected abstract function getNaming() : Naming;
    /**
     * The PHP Parser.
     */
    protected abstract function getParser() : Parser;
    protected function createProperty(Property $property, string $namespace, $default = null, bool $strict = \true) : Stmt
    {
        $propertyName = $property->getPhpName();
        $propertyStmt = new Stmt\PropertyProperty($propertyName);
        if (null === $default) {
            $default = $property->getDefault();
        }
        if (null !== $default && \is_scalar($default) || Type::TYPE_ARRAY === $property->getType()->getTypeHint($namespace) && \is_array($default)) {
            $propertyStmt->default = $this->getDefaultAsExpr($default)->expr;
        }
        return new Stmt\Property(Stmt\Class_::MODIFIER_PROTECTED, [$propertyStmt], ['comments' => [$this->createPropertyDoc($property, $namespace, $strict)]]);
    }
    protected function createPropertyDoc(Property $property, $namespace, bool $strict) : Doc
    {
        $docTypeHint = $property->getType()->getDocTypeHint($namespace);
        if ((!$strict || $property->isNullable()) && \strpos($docTypeHint, 'null') === \false) {
            $docTypeHint .= '|null';
        }
        $description = \sprintf(<<<EOD
/**
 * %s
 *

EOD
, $property->getDescription());
        if ($property->isDeprecated()) {
            $description .= <<<EOD
 * @deprecated
 *

EOD;
        }
        $description .= \sprintf(<<<EOD
 * @var %s
 */
EOD
, $docTypeHint);
        return new Doc($description);
    }
    private function getDefaultAsExpr($value) : Stmt\Expression
    {
        return $this->parser->parse('<?php ' . \var_export($value, \true) . ';')[0];
    }
}
