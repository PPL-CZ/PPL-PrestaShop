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
class NotNull extends Constraint
{
    public const IS_NULL_ERROR = 'ad32d13f-c3d4-423b-909a-857b961eb720';
    protected static $errorNames = [self::IS_NULL_ERROR => 'IS_NULL_ERROR'];
    public $message = 'This value should not be null.';
    public function __construct(?array $options = null, ?string $message = null, ?array $groups = null, $payload = null)
    {
        parent::__construct($options ?? [], $groups, $payload);
        $this->message = $message ?? $this->message;
    }
}
