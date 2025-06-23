<?php

namespace PPLShipping;

use PPLShipping\Symfony\Component\OptionsResolver\Options;
interface CustomQueryResolver
{
    public function __invoke(Options $options, $value);
}
\class_alias('PPLShipping\\CustomQueryResolver', 'CustomQueryResolver', \false);
