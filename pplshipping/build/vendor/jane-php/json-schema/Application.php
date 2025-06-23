<?php

namespace PPLShipping\Jane\Component\JsonSchema;

use PPLShipping\Jane\Component\JsonSchema\Console\Command\DumpConfigCommand;
use PPLShipping\Jane\Component\JsonSchema\Console\Command\GenerateCommand;
use PPLShipping\Jane\Component\JsonSchema\Console\Loader\ConfigLoader;
use PPLShipping\Jane\Component\JsonSchema\Console\Loader\SchemaLoader;
use PPLShipping\Symfony\Component\Console\Application as BaseApplication;
class Application extends BaseApplication
{
    public const VERSION = '6.x-dev';
    /**
     * Constructor.
     */
    public function __construct()
    {
        parent::__construct('Jane', self::VERSION);
        $this->boot();
    }
    protected function boot() : void
    {
        $configLoader = new ConfigLoader();
        $this->add(new GenerateCommand($configLoader, new SchemaLoader()));
        $this->add(new DumpConfigCommand($configLoader));
    }
}
