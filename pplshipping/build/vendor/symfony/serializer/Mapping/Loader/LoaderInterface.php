<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace PPLShipping\Symfony\Component\Serializer\Mapping\Loader;

use PPLShipping\Symfony\Component\Serializer\Mapping\ClassMetadataInterface;
/**
 * Loads {@link ClassMetadataInterface}.
 *
 * @author Kévin Dunglas <dunglas@gmail.com>
 */
interface LoaderInterface
{
    /**
     * @return bool
     */
    public function loadClassMetadata(ClassMetadataInterface $classMetadata);
}
