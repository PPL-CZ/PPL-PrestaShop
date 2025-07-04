<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace PPLShipping\Symfony\Component\Validator\Validator;

/**
 * A wrapper for a callable initializing a property from a getter.
 *
 * @internal
 */
class LazyProperty
{
    private $propertyValueCallback;
    public function __construct(\Closure $propertyValueCallback)
    {
        $this->propertyValueCallback = $propertyValueCallback;
    }
    public function getPropertyValue()
    {
        return ($this->propertyValueCallback)();
    }
}
