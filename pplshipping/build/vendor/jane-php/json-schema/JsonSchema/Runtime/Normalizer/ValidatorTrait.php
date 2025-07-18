<?php

namespace PPLShipping\Jane\Component\JsonSchema\JsonSchema\Runtime\Normalizer;

use PPLShipping\Symfony\Component\Validator\Constraint;
trait ValidatorTrait
{
    protected function validate(array $data, Constraint $constraint) : void
    {
        $validator = \PPLShipping\Symfony\Component\Validator\Validation::createValidator();
        $violations = $validator->validate($data, $constraint);
        if ($violations->count() > 0) {
            throw new ValidationException($violations);
        }
    }
}
