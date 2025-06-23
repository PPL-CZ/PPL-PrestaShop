<?php

declare (strict_types=1);
namespace PPLShipping\Doctrine\Inflector;

interface WordInflector
{
    public function inflect(string $word) : string;
}
