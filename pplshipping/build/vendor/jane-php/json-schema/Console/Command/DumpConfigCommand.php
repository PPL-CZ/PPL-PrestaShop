<?php

namespace PPLShipping\Jane\Component\JsonSchema\Console\Command;

use PPLShipping\Jane\Component\JsonSchema\Console\Loader\ConfigLoaderInterface;
use PPLShipping\Symfony\Component\Console\Command\Command;
use PPLShipping\Symfony\Component\Console\Input\InputInterface;
use PPLShipping\Symfony\Component\Console\Input\InputOption;
use PPLShipping\Symfony\Component\Console\Output\OutputInterface;
use PPLShipping\Symfony\Component\VarDumper\VarDumper;
class DumpConfigCommand extends Command
{
    /** @var ConfigLoaderInterface */
    protected $configLoader;
    public function __construct(ConfigLoaderInterface $configLoader)
    {
        parent::__construct(null);
        $this->configLoader = $configLoader;
    }
    public function configure()
    {
        $this->setName('dump-config');
        $this->setDescription('Dump Jane configuration for debugging purpose');
        $this->addOption('config-file', 'c', InputOption::VALUE_REQUIRED, 'File to use for Jane configuration', '.jane');
    }
    public function execute(InputInterface $input, OutputInterface $output)
    {
        VarDumper::dump($this->configLoader->load($input->getOption('config-file')));
        return 0;
    }
}
