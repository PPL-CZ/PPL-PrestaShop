<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace PPLShipping\Symfony\Component\Serializer\Mapping\Factory;

use PPLShipping\Symfony\Component\Serializer\Exception\InvalidArgumentException;
/**
 * Resolves a class name.
 *
 * @internal
 *
 * @author Kévin Dunglas <dunglas@gmail.com>
 */
trait ClassResolverTrait
{
    /**
     * Gets a class name for a given class or instance.
     *
     * @param object|string $value
     *
     * @throws InvalidArgumentException If the class does not exist
     */
    private function getClass($value) : string
    {
        if (\is_string($value)) {
            if (!\class_exists($value) && !\interface_exists($value, \false)) {
                throw new InvalidArgumentException(\sprintf('The class or interface "%s" does not exist.', $value));
            }
            return \ltrim($value, '\\');
        }
        if (!\is_object($value)) {
            throw new InvalidArgumentException(\sprintf('Cannot create metadata for non-objects. Got: "%s".', \get_debug_type($value)));
        }
        return \get_class($value);
    }
}
