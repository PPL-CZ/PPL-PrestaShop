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
 * Validates whether a value is a valid IP address.
 *
 * @author Bernhard Schussek <bschussek@gmail.com>
 * @author Joseph Bielawski <stloyd@gmail.com>
 */
class IpValidator extends ConstraintValidator
{
    /**
     * {@inheritdoc}
     */
    public function validate($value, Constraint $constraint)
    {
        if (!$constraint instanceof Ip) {
            throw new UnexpectedTypeException($constraint, Ip::class);
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
        switch ($constraint->version) {
            case Ip::V4:
                $flag = \FILTER_FLAG_IPV4;
                break;
            case Ip::V6:
                $flag = \FILTER_FLAG_IPV6;
                break;
            case Ip::V4_NO_PRIV:
                $flag = \FILTER_FLAG_IPV4 | \FILTER_FLAG_NO_PRIV_RANGE;
                break;
            case Ip::V6_NO_PRIV:
                $flag = \FILTER_FLAG_IPV6 | \FILTER_FLAG_NO_PRIV_RANGE;
                break;
            case Ip::ALL_NO_PRIV:
                $flag = \FILTER_FLAG_NO_PRIV_RANGE;
                break;
            case Ip::V4_NO_RES:
                $flag = \FILTER_FLAG_IPV4 | \FILTER_FLAG_NO_RES_RANGE;
                break;
            case Ip::V6_NO_RES:
                $flag = \FILTER_FLAG_IPV6 | \FILTER_FLAG_NO_RES_RANGE;
                break;
            case Ip::ALL_NO_RES:
                $flag = \FILTER_FLAG_NO_RES_RANGE;
                break;
            case Ip::V4_ONLY_PUBLIC:
                $flag = \FILTER_FLAG_IPV4 | \FILTER_FLAG_NO_PRIV_RANGE | \FILTER_FLAG_NO_RES_RANGE;
                break;
            case Ip::V6_ONLY_PUBLIC:
                $flag = \FILTER_FLAG_IPV6 | \FILTER_FLAG_NO_PRIV_RANGE | \FILTER_FLAG_NO_RES_RANGE;
                break;
            case Ip::ALL_ONLY_PUBLIC:
                $flag = \FILTER_FLAG_NO_PRIV_RANGE | \FILTER_FLAG_NO_RES_RANGE;
                break;
            default:
                $flag = 0;
                break;
        }
        if (!\filter_var($value, \FILTER_VALIDATE_IP, $flag)) {
            $this->context->buildViolation($constraint->message)->setParameter('{{ value }}', $this->formatValue($value))->setCode(Ip::INVALID_IP_ERROR)->addViolation();
        }
    }
}
