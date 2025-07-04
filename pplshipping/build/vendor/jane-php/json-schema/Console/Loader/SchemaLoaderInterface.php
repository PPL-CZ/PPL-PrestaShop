<?php

namespace PPLShipping\Jane\Component\JsonSchema\Console\Loader;

use PPLShipping\Jane\Component\JsonSchema\Registry\SchemaInterface;
interface SchemaLoaderInterface
{
    public function resolve(string $schema, array $options = []) : SchemaInterface;
}
