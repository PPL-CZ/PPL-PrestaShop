<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace PPLShipping\Symfony\Component\Validator\Constraints;

use PPLShipping\Symfony\Component\PropertyAccess\PropertyAccess;
use PPLShipping\Symfony\Component\Validator\Constraint;
use PPLShipping\Symfony\Component\Validator\Exception\ConstraintDefinitionException;
use PPLShipping\Symfony\Component\Validator\Exception\LogicException;
/**
 * Used for the comparison of values.
 *
 * @author Daniel Holmes <daniel@danielholmes.org>
 * @author Bernhard Schussek <bschussek@gmail.com>
 */
abstract class AbstractComparison extends Constraint
{
    public $message;
    public $value;
    public $propertyPath;
    /**
     * {@inheritdoc}
     *
     * @param mixed $value the value to compare or a set of options
     */
    public function __construct($value = null, $propertyPath = null, ?string $message = null, ?array $groups = null, $payload = null, array $options = [])
    {
        if (\is_array($value)) {
            $options = \array_merge($value, $options);
        } elseif (null !== $value) {
            $options['value'] = $value;
        }
        parent::__construct($options, $groups, $payload);
        $this->message = $message ?? $this->message;
        $this->propertyPath = $propertyPath ?? $this->propertyPath;
        if (null === $this->value && null === $this->propertyPath) {
            throw new ConstraintDefinitionException(\sprintf('The "%s" constraint requires either the "value" or "propertyPath" option to be set.', static::class));
        }
        if (null !== $this->value && null !== $this->propertyPath) {
            throw new ConstraintDefinitionException(\sprintf('The "%s" constraint requires only one of the "value" or "propertyPath" options to be set, not both.', static::class));
        }
        if (null !== $this->propertyPath && !\class_exists(PropertyAccess::class)) {
            throw new LogicException(\sprintf('The "%s" constraint requires the Symfony PropertyAccess component to use the "propertyPath" option.', static::class));
        }
    }
    /**
     * {@inheritdoc}
     */
    public function getDefaultOption()
    {
        return 'value';
    }
}
