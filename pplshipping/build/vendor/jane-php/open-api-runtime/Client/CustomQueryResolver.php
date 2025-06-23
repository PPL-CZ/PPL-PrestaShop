<?php

declare (strict_types=1);
namespace PPLShipping\Jane\Component\OpenApiRuntime\Client;

use PPLShipping\Symfony\Component\OptionsResolver\Options;
interface CustomQueryResolver
{
    public function __invoke(Options $options, $value);
}
