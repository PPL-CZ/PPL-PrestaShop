<?php

namespace PPLShipping\Jane\Component\JsonSchema\Guesser\JsonSchema;

use PPLShipping\Jane\Component\JsonSchema\Guesser\Guess\DateTimeType;
use PPLShipping\Jane\Component\JsonSchema\Guesser\Guess\Type;
use PPLShipping\Jane\Component\JsonSchema\Guesser\GuesserInterface;
use PPLShipping\Jane\Component\JsonSchema\Guesser\TypeGuesserInterface;
use PPLShipping\Jane\Component\JsonSchema\JsonSchema\Model\JsonSchema;
use PPLShipping\Jane\Component\JsonSchema\Registry\Registry;
class DateTimeGuesser implements GuesserInterface, TypeGuesserInterface
{
    /** @var string Format of date to use when normalized */
    private $outputDateFormat;
    /** @var string Format of date to use when denormalized */
    private $inputDateFormat;
    /**
     * @var bool|null
     */
    private $preferInterface;
    public function __construct(string $outputDateFormat = \DateTime::RFC3339, ?string $inputDateFormat = null, ?bool $preferInterface = null)
    {
        $this->outputDateFormat = $outputDateFormat;
        $this->inputDateFormat = $inputDateFormat;
        $this->preferInterface = $preferInterface;
    }
    /**
     * {@inheritdoc}
     */
    public function supportObject($object) : bool
    {
        $class = $this->getSchemaClass();
        return $object instanceof $class && 'string' === $object->getType() && 'date-time' === $object->getFormat();
    }
    /**
     * {@inheritdoc}
     */
    public function guessType($object, string $name, string $reference, Registry $registry) : Type
    {
        return new DateTimeType($object, $this->outputDateFormat, $this->inputDateFormat, $this->preferInterface);
    }
    protected function getSchemaClass() : string
    {
        return JsonSchema::class;
    }
}
