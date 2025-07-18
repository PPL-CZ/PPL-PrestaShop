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
 * @author Laurent Masforné <l.masforne@gmail.com>
 *
 * @see https://en.wikipedia.org/wiki/International_Securities_Identification_Number
 */
class IsinValidator extends ConstraintValidator
{
    /**
     * {@inheritdoc}
     */
    public function validate($value, Constraint $constraint)
    {
        if (!$constraint instanceof Isin) {
            throw new UnexpectedTypeException($constraint, Isin::class);
        }
        if (null === $value || '' === $value) {
            return;
        }
        if (!\is_scalar($value) && !(\is_object($value) && \method_exists($value, '__toString'))) {
            throw new UnexpectedValueException($value, 'string');
        }
        $value = \strtoupper($value);
        if (Isin::VALIDATION_LENGTH !== \strlen($value)) {
            $this->context->buildViolation($constraint->message)->setParameter('{{ value }}', $this->formatValue($value))->setCode(Isin::INVALID_LENGTH_ERROR)->addViolation();
            return;
        }
        if (!\preg_match(Isin::VALIDATION_PATTERN, $value)) {
            $this->context->buildViolation($constraint->message)->setParameter('{{ value }}', $this->formatValue($value))->setCode(Isin::INVALID_PATTERN_ERROR)->addViolation();
            return;
        }
        if (!$this->isCorrectChecksum($value)) {
            $this->context->buildViolation($constraint->message)->setParameter('{{ value }}', $this->formatValue($value))->setCode(Isin::INVALID_CHECKSUM_ERROR)->addViolation();
        }
    }
    private function isCorrectChecksum(string $input) : bool
    {
        $characters = \str_split($input);
        foreach ($characters as $i => $char) {
            $characters[$i] = \intval($char, 36);
        }
        $number = \implode('', $characters);
        return 0 === $this->context->getValidator()->validate($number, new Luhn())->count();
    }
}
