<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace PPLShipping\Symfony\Component\Serializer\Normalizer;

use PPLShipping\Symfony\Component\Serializer\Exception\CircularReferenceException;
use PPLShipping\Symfony\Component\Serializer\Exception\ExceptionInterface;
use PPLShipping\Symfony\Component\Serializer\Exception\InvalidArgumentException;
use PPLShipping\Symfony\Component\Serializer\Exception\LogicException;
/**
 * @author Jordi Boggiano <j.boggiano@seld.be>
 */
interface NormalizerInterface
{
    /**
     * Normalizes an object into a set of arrays/scalars.
     *
     * @param mixed       $object  Object to normalize
     * @param string|null $format  Format the normalization result will be encoded as
     * @param array       $context Context options for the normalizer
     *
     * @return array|string|int|float|bool|\ArrayObject|null \ArrayObject is used to make sure an empty object is encoded as an object not an array
     *
     * @throws InvalidArgumentException   Occurs when the object given is not a supported type for the normalizer
     * @throws CircularReferenceException Occurs when the normalizer detects a circular reference when no circular
     *                                    reference handler can fix it
     * @throws LogicException             Occurs when the normalizer is not called in an expected context
     * @throws ExceptionInterface         Occurs for all the other cases of errors
     */
    public function normalize($object, ?string $format = null, array $context = []);
    /**
     * Checks whether the given class is supported for normalization by this normalizer.
     *
     * @param mixed       $data   Data to normalize
     * @param string|null $format The format being (de-)serialized from or into
     *
     * @return bool
     */
    public function supportsNormalization($data, ?string $format = null);
}
