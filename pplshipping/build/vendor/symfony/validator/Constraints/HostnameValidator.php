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
 * @author Dmitrii Poddubnyi <dpoddubny@gmail.com>
 */
class HostnameValidator extends ConstraintValidator
{
    /**
     * https://tools.ietf.org/html/rfc2606.
     */
    private const RESERVED_TLDS = ['example', 'invalid', 'localhost', 'test'];
    public function validate($value, Constraint $constraint)
    {
        if (!$constraint instanceof Hostname) {
            throw new UnexpectedTypeException($constraint, Hostname::class);
        }
        if (null === $value || '' === $value) {
            return;
        }
        if (!\is_scalar($value) && !(\is_object($value) && \method_exists($value, '__toString'))) {
            throw new UnexpectedValueException($value, 'string');
        }
        $value = (string) $value;
        if ('' === $value) {
            return;
        }
        if (!$this->isValid($value) || $constraint->requireTld && !$this->hasValidTld($value)) {
            $this->context->buildViolation($constraint->message)->setParameter('{{ value }}', $this->formatValue($value))->setCode(Hostname::INVALID_HOSTNAME_ERROR)->addViolation();
        }
    }
    private function isValid(string $domain) : bool
    {
        return \false !== \filter_var($domain, \FILTER_VALIDATE_DOMAIN, \FILTER_FLAG_HOSTNAME);
    }
    private function hasValidTld(string $domain) : bool
    {
        return \false !== \strpos($domain, '.') && !\in_array(\substr($domain, \strrpos($domain, '.') + 1), self::RESERVED_TLDS, \true);
    }
}
