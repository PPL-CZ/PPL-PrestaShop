<?php

declare (strict_types=1);
namespace PPLShipping\Doctrine\Inflector\Rules\NorwegianBokmal;

use PPLShipping\Doctrine\Inflector\GenericLanguageInflectorFactory;
use PPLShipping\Doctrine\Inflector\Rules\Ruleset;
final class InflectorFactory extends GenericLanguageInflectorFactory
{
    protected function getSingularRuleset() : Ruleset
    {
        return Rules::getSingularRuleset();
    }
    protected function getPluralRuleset() : Ruleset
    {
        return Rules::getPluralRuleset();
    }
}
