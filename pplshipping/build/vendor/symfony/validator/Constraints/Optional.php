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
 * @Target({"ANNOTATION"})
 *
 * @author Bernhard Schussek <bschussek@gmail.com>
 */
class Optional extends Existence
{
}
