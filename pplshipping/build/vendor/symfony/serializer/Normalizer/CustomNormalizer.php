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

use PPLShipping\Symfony\Component\Serializer\SerializerAwareInterface;
use PPLShipping\Symfony\Component\Serializer\SerializerAwareTrait;
/**
 * @author Jordi Boggiano <j.boggiano@seld.be>
 */
class CustomNormalizer implements NormalizerInterface, DenormalizerInterface, SerializerAwareInterface, CacheableSupportsMethodInterface
{
    use ObjectToPopulateTrait;
    use SerializerAwareTrait;
    /**
     * {@inheritdoc}
     */
    public function normalize($object, ?string $format = null, array $context = [])
    {
        return $object->normalize($this->serializer, $format, $context);
    }
    /**
     * {@inheritdoc}
     */
    public function denormalize($data, string $type, ?string $format = null, array $context = [])
    {
        $object = $this->extractObjectToPopulate($type, $context) ?? new $type();
        $object->denormalize($this->serializer, $data, $format, $context);
        return $object;
    }
    /**
     * Checks if the given class implements the NormalizableInterface.
     *
     * @param mixed       $data   Data to normalize
     * @param string|null $format The format being (de-)serialized from or into
     *
     * @return bool
     */
    public function supportsNormalization($data, ?string $format = null)
    {
        return $data instanceof NormalizableInterface;
    }
    /**
     * Checks if the given class implements the DenormalizableInterface.
     *
     * @param mixed       $data   Data to denormalize from
     * @param string      $type   The class to which the data should be denormalized
     * @param string|null $format The format being deserialized from
     *
     * @return bool
     */
    public function supportsDenormalization($data, string $type, ?string $format = null)
    {
        return \is_subclass_of($type, DenormalizableInterface::class);
    }
    /**
     * {@inheritdoc}
     */
    public function hasCacheableSupportsMethod() : bool
    {
        return __CLASS__ === static::class;
    }
}
