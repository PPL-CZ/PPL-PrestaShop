<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace PPLShipping\Symfony\Component\Validator\Validator;

use PPLShipping\Symfony\Component\Validator\Constraint;
use PPLShipping\Symfony\Component\Validator\Constraints\GroupSequence;
use PPLShipping\Symfony\Component\Validator\ConstraintViolationListInterface;
/**
 * A validator in a specific execution context.
 *
 * @author Bernhard Schussek <bschussek@gmail.com>
 */
interface ContextualValidatorInterface
{
    /**
     * Appends the given path to the property path of the context.
     *
     * If called multiple times, the path will always be reset to the context's
     * original path with the given path appended to it.
     *
     * @return $this
     */
    public function atPath(string $path);
    /**
     * Validates a value against a constraint or a list of constraints.
     *
     * If no constraint is passed, the constraint
     * {@link \Symfony\Component\Validator\Constraints\Valid} is assumed.
     *
     * @param mixed                                                 $value       The value to validate
     * @param Constraint|Constraint[]|null                          $constraints The constraint(s) to validate against
     * @param string|GroupSequence|array<string|GroupSequence>|null $groups      The validation groups to validate. If none is given, "Default" is assumed
     *
     * @return $this
     */
    public function validate($value, $constraints = null, $groups = null);
    /**
     * Validates a property of an object against the constraints specified
     * for this property.
     *
     * @param string                                                $propertyName The name of the validated property
     * @param string|GroupSequence|array<string|GroupSequence>|null $groups       The validation groups to validate. If none is given, "Default" is assumed
     *
     * @return $this
     */
    public function validateProperty(object $object, string $propertyName, $groups = null);
    /**
     * Validates a value against the constraints specified for an object's
     * property.
     *
     * @param object|string                                         $objectOrClass The object or its class name
     * @param string                                                $propertyName  The name of the property
     * @param mixed                                                 $value         The value to validate against the property's constraints
     * @param string|GroupSequence|array<string|GroupSequence>|null $groups        The validation groups to validate. If none is given, "Default" is assumed
     *
     * @return $this
     */
    public function validatePropertyValue($objectOrClass, string $propertyName, $value, $groups = null);
    /**
     * Returns the violations that have been generated so far in the context
     * of the validator.
     *
     * @return ConstraintViolationListInterface
     */
    public function getViolations();
}
