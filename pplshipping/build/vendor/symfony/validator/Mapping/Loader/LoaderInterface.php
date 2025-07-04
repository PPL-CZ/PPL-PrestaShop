<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace PPLShipping\Symfony\Component\Validator\Mapping\Loader;

use PPLShipping\Symfony\Component\Validator\Mapping\ClassMetadata;
/**
 * Loads validation metadata into {@link ClassMetadata} instances.
 *
 * @author Bernhard Schussek <bschussek@gmail.com>
 */
interface LoaderInterface
{
    /**
     * Loads validation metadata into a {@link ClassMetadata} instance.
     *
     * @return bool
     */
    public function loadClassMetadata(ClassMetadata $metadata);
}
