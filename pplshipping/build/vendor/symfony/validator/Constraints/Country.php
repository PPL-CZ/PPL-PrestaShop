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

use PPLShipping\Symfony\Component\Intl\Countries;
use PPLShipping\Symfony\Component\Validator\Constraint;
use PPLShipping\Symfony\Component\Validator\Exception\LogicException;
/**
 * @Annotation
 * @Target({"PROPERTY", "METHOD", "ANNOTATION"})
 *
 * @author Bernhard Schussek <bschussek@gmail.com>
 */
#[\Attribute(\Attribute::TARGET_PROPERTY | \Attribute::TARGET_METHOD | \Attribute::IS_REPEATABLE)]
class Country extends Constraint
{
    public const NO_SUCH_COUNTRY_ERROR = '8f900c12-61bd-455d-9398-996cd040f7f0';
    protected static $errorNames = [self::NO_SUCH_COUNTRY_ERROR => 'NO_SUCH_COUNTRY_ERROR'];
    public $message = 'This value is not a valid country.';
    public $alpha3 = \false;
    public function __construct(?array $options = null, ?string $message = null, ?bool $alpha3 = null, ?array $groups = null, $payload = null)
    {
        if (!\class_exists(Countries::class)) {
            throw new LogicException('The Intl component is required to use the Country constraint. Try running "composer require symfony/intl".');
        }
        parent::__construct($options, $groups, $payload);
        $this->message = $message ?? $this->message;
        $this->alpha3 = $alpha3 ?? $this->alpha3;
    }
}
