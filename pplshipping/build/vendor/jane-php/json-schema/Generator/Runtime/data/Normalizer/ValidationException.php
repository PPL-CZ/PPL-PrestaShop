<?php

namespace PPLShipping;

use PPLShipping\Symfony\Component\Validator\ConstraintViolationListInterface;
class ValidationException extends \RuntimeException
{
    /** @var ConstraintViolationListInterface */
    private $violationList;
    public function __construct(ConstraintViolationListInterface $violationList)
    {
        $this->violationList = $violationList;
        parent::__construct(\sprintf('Model validation failed with %d errors.', $violationList->count()), 400);
    }
    public function getViolationList() : ConstraintViolationListInterface
    {
        return $this->violationList;
    }
}
\class_alias('PPLShipping\\ValidationException', 'ValidationException', \false);
