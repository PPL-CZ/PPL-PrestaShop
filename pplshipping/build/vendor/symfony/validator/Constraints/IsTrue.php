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
/**
 * @Annotation
 * @Target({"PROPERTY", "METHOD", "ANNOTATION"})
 *
 * @author Bernhard Schussek <bschussek@gmail.com>
 */
#[\Attribute(\Attribute::TARGET_PROPERTY | \Attribute::TARGET_METHOD | \Attribute::IS_REPEATABLE)]
class IsTrue extends Constraint
{
    public const NOT_TRUE_ERROR = '2beabf1c-54c0-4882-a928-05249b26e23b';
    protected static $errorNames = [self::NOT_TRUE_ERROR => 'NOT_TRUE_ERROR'];
    public $message = 'This value should be true.';
    public function __construct(?array $options = null, ?string $message = null, ?array $groups = null, $payload = null)
    {
        parent::__construct($options ?? [], $groups, $payload);
        $this->message = $message ?? $this->message;
    }
}
