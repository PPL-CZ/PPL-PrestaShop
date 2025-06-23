<?php

namespace PPLShipping\Jane\Component\JsonSchema\Tools;

use PPLShipping\Doctrine\Inflector\Inflector;
use PPLShipping\Doctrine\Inflector\InflectorFactory;
trait InflectorTrait
{
    private $inflector;
    protected function getInflector() : Inflector
    {
        if (null === $this->inflector) {
            $this->inflector = InflectorFactory::create()->build();
        }
        return $this->inflector;
    }
}
