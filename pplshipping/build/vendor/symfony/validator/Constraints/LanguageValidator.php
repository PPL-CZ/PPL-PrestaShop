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

use PPLShipping\Symfony\Component\Intl\Languages;
use PPLShipping\Symfony\Component\Validator\Constraint;
use PPLShipping\Symfony\Component\Validator\ConstraintValidator;
use PPLShipping\Symfony\Component\Validator\Exception\UnexpectedTypeException;
use PPLShipping\Symfony\Component\Validator\Exception\UnexpectedValueException;
/**
 * Validates whether a value is a valid language code.
 *
 * @author Bernhard Schussek <bschussek@gmail.com>
 */
class LanguageValidator extends ConstraintValidator
{
    /**
     * {@inheritdoc}
     */
    public function validate($value, Constraint $constraint)
    {
        if (!$constraint instanceof Language) {
            throw new UnexpectedTypeException($constraint, Language::class);
        }
        if (null === $value || '' === $value) {
            return;
        }
        if (!\is_scalar($value) && !(\is_object($value) && \method_exists($value, '__toString'))) {
            throw new UnexpectedValueException($value, 'string');
        }
        $value = (string) $value;
        if ($constraint->alpha3 ? !Languages::alpha3CodeExists($value) : !Languages::exists($value)) {
            $this->context->buildViolation($constraint->message)->setParameter('{{ value }}', $this->formatValue($value))->setCode(Language::NO_SUCH_LANGUAGE_ERROR)->addViolation();
        }
    }
}
