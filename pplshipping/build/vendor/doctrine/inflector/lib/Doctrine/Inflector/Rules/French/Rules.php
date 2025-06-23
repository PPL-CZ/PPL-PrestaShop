<?php

declare (strict_types=1);
namespace PPLShipping\Doctrine\Inflector\Rules\French;

use PPLShipping\Doctrine\Inflector\Rules\Patterns;
use PPLShipping\Doctrine\Inflector\Rules\Ruleset;
use PPLShipping\Doctrine\Inflector\Rules\Substitutions;
use PPLShipping\Doctrine\Inflector\Rules\Transformations;
final class Rules
{
    public static function getSingularRuleset() : Ruleset
    {
        return new Ruleset(new Transformations(...Inflectible::getSingular()), new Patterns(...Uninflected::getSingular()), (new Substitutions(...Inflectible::getIrregular()))->getFlippedSubstitutions());
    }
    public static function getPluralRuleset() : Ruleset
    {
        return new Ruleset(new Transformations(...Inflectible::getPlural()), new Patterns(...Uninflected::getPlural()), new Substitutions(...Inflectible::getIrregular()));
    }
}
