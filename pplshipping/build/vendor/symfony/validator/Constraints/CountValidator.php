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

use PPLShipping\Symfony\Component\Validator\Constraint;
use PPLShipping\Symfony\Component\Validator\ConstraintValidator;
use PPLShipping\Symfony\Component\Validator\Exception\UnexpectedTypeException;
use PPLShipping\Symfony\Component\Validator\Exception\UnexpectedValueException;
/**
 * @author Bernhard Schussek <bschussek@gmail.com>
 */
class CountValidator extends ConstraintValidator
{
    /**
     * {@inheritdoc}
     */
    public function validate($value, Constraint $constraint)
    {
        if (!$constraint instanceof Count) {
            throw new UnexpectedTypeException($constraint, Count::class);
        }
        if (null === $value) {
            return;
        }
        if (!\is_array($value) && !$value instanceof \Countable) {
            throw new UnexpectedValueException($value, 'array|\\Countable');
        }
        $count = \count($value);
        if (null !== $constraint->max && $count > $constraint->max) {
            $exactlyOptionEnabled = $constraint->min == $constraint->max;
            $this->context->buildViolation($exactlyOptionEnabled ? $constraint->exactMessage : $constraint->maxMessage)->setParameter('{{ count }}', $count)->setParameter('{{ limit }}', $constraint->max)->setInvalidValue($value)->setPlural((int) $constraint->max)->setCode($exactlyOptionEnabled ? Count::NOT_EQUAL_COUNT_ERROR : Count::TOO_MANY_ERROR)->addViolation();
            return;
        }
        if (null !== $constraint->min && $count < $constraint->min) {
            $exactlyOptionEnabled = $constraint->min == $constraint->max;
            $this->context->buildViolation($exactlyOptionEnabled ? $constraint->exactMessage : $constraint->minMessage)->setParameter('{{ count }}', $count)->setParameter('{{ limit }}', $constraint->min)->setInvalidValue($value)->setPlural((int) $constraint->min)->setCode($exactlyOptionEnabled ? Count::NOT_EQUAL_COUNT_ERROR : Count::TOO_FEW_ERROR)->addViolation();
            return;
        }
        if (null !== $constraint->divisibleBy) {
            $this->context->getValidator()->inContext($this->context)->validate($count, [new DivisibleBy(['value' => $constraint->divisibleBy, 'message' => $constraint->divisibleByMessage])], $this->context->getGroup());
        }
    }
}
