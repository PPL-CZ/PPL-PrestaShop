<?php

namespace PPLShipping\Jane\Component\OpenApiCommon;

use PPLShipping\Jane\Component\JsonSchema\Application as JsonSchemaApplication;
use PPLShipping\Jane\Component\OpenApiCommon\Console\Command\DumpConfigCommand;
use PPLShipping\Jane\Component\OpenApiCommon\Console\Command\GenerateCommand;
use PPLShipping\Jane\Component\OpenApiCommon\Console\Loader\ConfigLoader;
use PPLShipping\Jane\Component\OpenApiCommon\Console\Loader\OpenApiMatcher;
use PPLShipping\Jane\Component\OpenApiCommon\Console\Loader\SchemaLoader;
class Application extends JsonSchemaApplication
{
    protected function boot() : void
    {
        $configLoader = new ConfigLoader();
        $this->add(new GenerateCommand($configLoader, new SchemaLoader(), new OpenApiMatcher()));
        $this->add(new DumpConfigCommand($configLoader));
    }
}
