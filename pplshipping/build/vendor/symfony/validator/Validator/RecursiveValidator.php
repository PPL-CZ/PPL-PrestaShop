<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace PPLShipping\Symfony\Component\Validator\Validator;

use PPLShipping\Symfony\Component\Validator\ConstraintValidatorFactoryInterface;
use PPLShipping\Symfony\Component\Validator\Context\ExecutionContextFactoryInterface;
use PPLShipping\Symfony\Component\Validator\Context\ExecutionContextInterface;
use PPLShipping\Symfony\Component\Validator\Mapping\Factory\MetadataFactoryInterface;
use PPLShipping\Symfony\Component\Validator\ObjectInitializerInterface;
/**
 * Recursive implementation of {@link ValidatorInterface}.
 *
 * @author Bernhard Schussek <bschussek@gmail.com>
 */
class RecursiveValidator implements ValidatorInterface
{
    protected $contextFactory;
    protected $metadataFactory;
    protected $validatorFactory;
    protected $objectInitializers;
    /**
     * Creates a new validator.
     *
     * @param ObjectInitializerInterface[] $objectInitializers The object initializers
     */
    public function __construct(ExecutionContextFactoryInterface $contextFactory, MetadataFactoryInterface $metadataFactory, ConstraintValidatorFactoryInterface $validatorFactory, array $objectInitializers = [])
    {
        $this->contextFactory = $contextFactory;
        $this->metadataFactory = $metadataFactory;
        $this->validatorFactory = $validatorFactory;
        $this->objectInitializers = $objectInitializers;
    }
    /**
     * {@inheritdoc}
     */
    public function startContext($root = null)
    {
        return new RecursiveContextualValidator($this->contextFactory->createContext($this, $root), $this->metadataFactory, $this->validatorFactory, $this->objectInitializers);
    }
    /**
     * {@inheritdoc}
     */
    public function inContext(ExecutionContextInterface $context)
    {
        return new RecursiveContextualValidator($context, $this->metadataFactory, $this->validatorFactory, $this->objectInitializers);
    }
    /**
     * {@inheritdoc}
     */
    public function getMetadataFor($object)
    {
        return $this->metadataFactory->getMetadataFor($object);
    }
    /**
     * {@inheritdoc}
     */
    public function hasMetadataFor($object)
    {
        return $this->metadataFactory->hasMetadataFor($object);
    }
    /**
     * {@inheritdoc}
     */
    public function validate($value, $constraints = null, $groups = null)
    {
        return $this->startContext($value)->validate($value, $constraints, $groups)->getViolations();
    }
    /**
     * {@inheritdoc}
     */
    public function validateProperty(object $object, string $propertyName, $groups = null)
    {
        return $this->startContext($object)->validateProperty($object, $propertyName, $groups)->getViolations();
    }
    /**
     * {@inheritdoc}
     */
    public function validatePropertyValue($objectOrClass, string $propertyName, $value, $groups = null)
    {
        // If a class name is passed, take $value as root
        return $this->startContext(\is_object($objectOrClass) ? $objectOrClass : $value)->validatePropertyValue($objectOrClass, $propertyName, $value, $groups)->getViolations();
    }
}
