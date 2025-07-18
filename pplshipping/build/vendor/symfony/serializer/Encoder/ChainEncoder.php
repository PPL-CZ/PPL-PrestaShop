<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace PPLShipping\Symfony\Component\Serializer\Encoder;

use PPLShipping\Symfony\Component\Serializer\Exception\RuntimeException;
/**
 * Encoder delegating the decoding to a chain of encoders.
 *
 * @author Jordi Boggiano <j.boggiano@seld.be>
 * @author Johannes M. Schmitt <schmittjoh@gmail.com>
 * @author Lukas Kahwe Smith <smith@pooteeweet.org>
 *
 * @final
 */
class ChainEncoder implements ContextAwareEncoderInterface
{
    protected $encoders = [];
    protected $encoderByFormat = [];
    public function __construct(array $encoders = [])
    {
        $this->encoders = $encoders;
    }
    /**
     * {@inheritdoc}
     */
    public final function encode($data, string $format, array $context = []) : string
    {
        return $this->getEncoder($format, $context)->encode($data, $format, $context);
    }
    /**
     * {@inheritdoc}
     */
    public function supportsEncoding(string $format, array $context = []) : bool
    {
        try {
            $this->getEncoder($format, $context);
        } catch (RuntimeException $e) {
            return \false;
        }
        return \true;
    }
    /**
     * Checks whether the normalization is needed for the given format.
     */
    public function needsNormalization(string $format, array $context = []) : bool
    {
        $encoder = $this->getEncoder($format, $context);
        if (!$encoder instanceof NormalizationAwareInterface) {
            return \true;
        }
        if ($encoder instanceof self) {
            return $encoder->needsNormalization($format, $context);
        }
        return \false;
    }
    /**
     * Gets the encoder supporting the format.
     *
     * @throws RuntimeException if no encoder is found
     */
    private function getEncoder(string $format, array $context) : EncoderInterface
    {
        if (isset($this->encoderByFormat[$format]) && isset($this->encoders[$this->encoderByFormat[$format]])) {
            return $this->encoders[$this->encoderByFormat[$format]];
        }
        $cache = \true;
        foreach ($this->encoders as $i => $encoder) {
            $cache = $cache && !$encoder instanceof ContextAwareEncoderInterface;
            if ($encoder->supportsEncoding($format, $context)) {
                if ($cache) {
                    $this->encoderByFormat[$format] = $i;
                }
                return $encoder;
            }
        }
        throw new RuntimeException(\sprintf('No encoder found for format "%s".', $format));
    }
}
