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
class DateTime extends Constraint
{
    public const INVALID_FORMAT_ERROR = '1a9da513-2640-4f84-9b6a-4d99dcddc628';
    public const INVALID_DATE_ERROR = 'd52afa47-620d-4d99-9f08-f4d85b36e33c';
    public const INVALID_TIME_ERROR = '5e797c9d-74f7-4098-baa3-94390c447b27';
    protected static $errorNames = [self::INVALID_FORMAT_ERROR => 'INVALID_FORMAT_ERROR', self::INVALID_DATE_ERROR => 'INVALID_DATE_ERROR', self::INVALID_TIME_ERROR => 'INVALID_TIME_ERROR'];
    public $format = 'Y-m-d H:i:s';
    public $message = 'This value is not a valid datetime.';
    /**
     * {@inheritdoc}
     *
     * @param string|array|null $format
     */
    public function __construct($format = null, ?string $message = null, ?array $groups = null, $payload = null, array $options = [])
    {
        if (\is_array($format)) {
            $options = \array_merge($format, $options);
        } elseif (null !== $format) {
            $options['value'] = $format;
        }
        parent::__construct($options, $groups, $payload);
        $this->message = $message ?? $this->message;
    }
    public function getDefaultOption()
    {
        return 'format';
    }
}
