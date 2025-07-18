<?php

declare (strict_types=1);
namespace PPLShipping\Jane\Component\OpenApi3\Guesser\OpenApiSchema;

use PPLShipping\Jane\Component\JsonSchema\Generator\Naming;
use PPLShipping\Jane\Component\JsonSchema\Guesser\ChainGuesserAwareInterface;
use PPLShipping\Jane\Component\JsonSchema\Guesser\ChainGuesserAwareTrait;
use PPLShipping\Jane\Component\JsonSchema\Guesser\Guess\MultipleType;
use PPLShipping\Jane\Component\JsonSchema\Guesser\Guess\Type;
use PPLShipping\Jane\Component\JsonSchema\Guesser\GuesserInterface;
use PPLShipping\Jane\Component\JsonSchema\Guesser\GuesserResolverTrait;
use PPLShipping\Jane\Component\JsonSchema\Guesser\TypeGuesserInterface;
use PPLShipping\Jane\Component\JsonSchema\Registry\Registry;
use PPLShipping\Jane\Component\JsonSchemaRuntime\Reference;
use PPLShipping\Jane\Component\OpenApi3\JsonSchema\Model\Schema;
use PPLShipping\Symfony\Component\Serializer\SerializerInterface;
class AnyOfReferencefGuesser implements ChainGuesserAwareInterface, GuesserInterface, TypeGuesserInterface
{
    use ChainGuesserAwareTrait;
    use GuesserResolverTrait;
    protected $schemaClass;
    protected $naming;
    public function __construct(SerializerInterface $serializer, Naming $naming, string $schemaClass)
    {
        $this->serializer = $serializer;
        $this->schemaClass = $schemaClass;
        $this->naming = $naming;
    }
    public function supportObject($object) : bool
    {
        return $object instanceof Schema && \is_array($object->getAnyOf()) && $object->getAnyOf()[0] instanceof Reference;
    }
    public function guessType($object, string $name, string $reference, Registry $registry) : Type
    {
        $type = new MultipleType($object);
        if ($object instanceof Schema) {
            foreach ($object->getAnyOf() as $index => $anyOf) {
                if ($anyOf === null) {
                    continue;
                }
                $anyOfSchema = $anyOf;
                $anyOfReference = $reference . '/anyOf/' . $index;
                if ($anyOf instanceof Reference) {
                    $anyOfReference = (string) $anyOf->getMergedUri();
                    if ((string) $anyOf->getMergedUri() === (string) $anyOf->getMergedUri()->withFragment('')) {
                        $anyOfReference .= '#';
                    }
                    $anyOfSchema = $this->resolve($anyOfSchema, $this->schemaClass);
                }
                if (null !== $anyOfSchema->getType()) {
                    $anyOfType = $this->chainGuesser->guessType($anyOfSchema, $name, $anyOfReference, $registry);
                    $type->addType($anyOfType);
                }
            }
        }
        return $type;
    }
}
