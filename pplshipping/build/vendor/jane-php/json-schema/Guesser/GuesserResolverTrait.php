<?php

namespace PPLShipping\Jane\Component\JsonSchema\Guesser;

use PPLShipping\Jane\Component\JsonSchemaRuntime\Reference;
use PPLShipping\Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
trait GuesserResolverTrait
{
    /** @var DenormalizerInterface */
    protected $serializer;
    /**
     * Resolve a reference with a denormalizer.
     */
    public function resolve(Reference $reference, string $class) : object
    {
        $result = $reference;
        while ($result instanceof Reference) {
            $result = $result->resolve(function ($data) use($result, $class) {
                return $this->serializer->denormalize($data, $class, 'json', ['document-origin' => (string) $result->getMergedUri()->withFragment('')]);
            });
        }
        return $result;
    }
}
