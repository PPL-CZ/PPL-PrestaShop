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
class EqualTo extends AbstractComparison
{
    public const NOT_EQUAL_ERROR = '478618a7-95ba-473d-9101-cabd45e49115';
    protected static $errorNames = [self::NOT_EQUAL_ERROR => 'NOT_EQUAL_ERROR'];
    public $message = 'This value should be equal to {{ compared_value }}.';
}
