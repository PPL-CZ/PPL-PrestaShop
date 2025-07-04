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
 * Validates whether a value match or not given regexp pattern.
 *
 * @author Bernhard Schussek <bschussek@gmail.com>
 * @author Joseph Bielawski <stloyd@gmail.com>
 */
class RegexValidator extends ConstraintValidator
{
    /**
     * {@inheritdoc}
     */
    public function validate($value, Constraint $constraint)
    {
        if (!$constraint instanceof Regex) {
            throw new UnexpectedTypeException($constraint, Regex::class);
        }
        if (null === $value || '' === $value) {
            return;
        }
        if (!\is_scalar($value) && !(\is_object($value) && \method_exists($value, '__toString'))) {
            throw new UnexpectedValueException($value, 'string');
        }
        $value = (string) $value;
        if (null !== $constraint->normalizer) {
            $value = ($constraint->normalizer)($value);
        }
        if ($constraint->match xor \preg_match($constraint->pattern, $value)) {
            $this->context->buildViolation($constraint->message)->setParameter('{{ value }}', $this->formatValue($value))->setCode(Regex::REGEX_FAILED_ERROR)->addViolation();
        }
    }
}
