<?php

namespace PPLShipping\Jane\Component\JsonSchema\Guesser\JsonSchema;

use PPLShipping\Jane\Component\JsonSchema\Guesser\Guess\DateType;
use PPLShipping\Jane\Component\JsonSchema\Guesser\Guess\Type;
use PPLShipping\Jane\Component\JsonSchema\Guesser\GuesserInterface;
use PPLShipping\Jane\Component\JsonSchema\Guesser\TypeGuesserInterface;
use PPLShipping\Jane\Component\JsonSchema\JsonSchema\Model\JsonSchema;
use PPLShipping\Jane\Component\JsonSchema\Registry\Registry;
class DateGuesser implements GuesserInterface, TypeGuesserInterface
{
    /** @var string Format of date to use */
    private $dateFormat;
    /**
     * Indicator whether to use DateTime or DateTimeInterface as type hint.
     *
     * @var bool
     */
    private $preferInterface;
    public function __construct(string $dateFormat = 'Y-m-d', ?bool $preferInterface = null)
    {
        $this->dateFormat = $dateFormat;
        $this->preferInterface = $preferInterface;
    }
    /**
     * {@inheritdoc}
     */
    public function supportObject($object) : bool
    {
        $class = $this->getSchemaClass();
        return $object instanceof $class && 'string' === $object->getType() && 'date' === $object->getFormat();
    }
    /**
     * {@inheritdoc}
     */
    public function guessType($object, string $name, string $reference, Registry $registry) : Type
    {
        return new DateType($object, $this->dateFormat, $this->preferInterface);
    }
    protected function getSchemaClass() : string
    {
        return JsonSchema::class;
    }
}
