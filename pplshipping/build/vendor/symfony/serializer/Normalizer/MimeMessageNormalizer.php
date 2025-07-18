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

use PPLShipping\Symfony\Component\Mime\Address;
use PPLShipping\Symfony\Component\Mime\Header\HeaderInterface;
use PPLShipping\Symfony\Component\Mime\Header\Headers;
use PPLShipping\Symfony\Component\Mime\Header\UnstructuredHeader;
use PPLShipping\Symfony\Component\Mime\Message;
use PPLShipping\Symfony\Component\Mime\Part\AbstractPart;
use PPLShipping\Symfony\Component\Mime\RawMessage;
use PPLShipping\Symfony\Component\Serializer\SerializerAwareInterface;
use PPLShipping\Symfony\Component\Serializer\SerializerInterface;
/**
 * Normalize Mime message classes.
 *
 * It forces the use of a PropertyNormalizer instance for normalization
 * of all data objects composing a Message.
 *
 * Emails using resources for any parts are not serializable.
 */
final class MimeMessageNormalizer implements NormalizerInterface, DenormalizerInterface, SerializerAwareInterface, CacheableSupportsMethodInterface
{
    private $serializer;
    private $normalizer;
    private $headerClassMap;
    private $headersProperty;
    public function __construct(PropertyNormalizer $normalizer)
    {
        $this->normalizer = $normalizer;
        $this->headerClassMap = (new \ReflectionClassConstant(Headers::class, 'HEADER_CLASS_MAP'))->getValue();
        $this->headersProperty = new \ReflectionProperty(Headers::class, 'headers');
        $this->headersProperty->setAccessible(\true);
    }
    public function setSerializer(SerializerInterface $serializer)
    {
        $this->serializer = $serializer;
        $this->normalizer->setSerializer($serializer);
    }
    /**
     * {@inheritdoc}
     */
    public function normalize($object, ?string $format = null, array $context = [])
    {
        if ($object instanceof Headers) {
            $ret = [];
            foreach ($this->headersProperty->getValue($object) as $name => $header) {
                $ret[$name] = $this->serializer->normalize($header, $format, $context);
            }
            return $ret;
        }
        $ret = $this->normalizer->normalize($object, $format, $context);
        if ($object instanceof AbstractPart) {
            $ret['class'] = \get_class($object);
            unset($ret['seekable'], $ret['cid'], $ret['handle']);
        }
        if ($object instanceof RawMessage && \array_key_exists('message', $ret) && null === $ret['message']) {
            unset($ret['message']);
        }
        return $ret;
    }
    /**
     * {@inheritdoc}
     */
    public function denormalize($data, string $type, ?string $format = null, array $context = [])
    {
        if (Headers::class === $type) {
            $ret = [];
            foreach ($data as $headers) {
                foreach ($headers as $header) {
                    $ret[] = $this->serializer->denormalize($header, $this->headerClassMap[\strtolower($header['name'])] ?? UnstructuredHeader::class, $format, $context);
                }
            }
            return new Headers(...$ret);
        }
        if (AbstractPart::class === $type) {
            $type = $data['class'];
            unset($data['class']);
            $data['headers'] = $this->serializer->denormalize($data['headers'], Headers::class, $format, $context);
        }
        return $this->normalizer->denormalize($data, $type, $format, $context);
    }
    /**
     * {@inheritdoc}
     */
    public function supportsNormalization($data, ?string $format = null) : bool
    {
        return $data instanceof Message || $data instanceof Headers || $data instanceof HeaderInterface || $data instanceof Address || $data instanceof AbstractPart;
    }
    /**
     * {@inheritdoc}
     */
    public function supportsDenormalization($data, string $type, ?string $format = null) : bool
    {
        return \is_a($type, Message::class, \true) || Headers::class === $type || AbstractPart::class === $type;
    }
    /**
     * {@inheritdoc}
     */
    public function hasCacheableSupportsMethod() : bool
    {
        return __CLASS__ === static::class;
    }
}
