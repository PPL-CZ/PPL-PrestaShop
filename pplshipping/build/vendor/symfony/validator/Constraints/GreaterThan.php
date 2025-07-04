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

/**
 * @Annotation
 * @Target({"PROPERTY", "METHOD", "ANNOTATION"})
 *
 * @author Daniel Holmes <daniel@danielholmes.org>
 * @author Bernhard Schussek <bschussek@gmail.com>
 */
#[\Attribute(\Attribute::TARGET_PROPERTY | \Attribute::TARGET_METHOD | \Attribute::IS_REPEATABLE)]
class GreaterThan extends AbstractComparison
{
    public const TOO_LOW_ERROR = '778b7ae0-84d3-481a-9dec-35fdb64b1d78';
    protected static $errorNames = [self::TOO_LOW_ERROR => 'TOO_LOW_ERROR'];
    public $message = 'This value should be greater than {{ compared_value }}.';
}
