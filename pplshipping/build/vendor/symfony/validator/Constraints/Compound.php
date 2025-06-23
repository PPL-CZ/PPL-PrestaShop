<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace PPLShipping\Symfony\Component\Validator\Constraints;

use PPLShipping\Symfony\Component\Validator\Constraint;
use PPLShipping\Symfony\Component\Validator\Exception\ConstraintDefinitionException;
/**
 * Extend this class to create a reusable set of constraints.
 *
 * @author Maxime Steinhausser <maxime.steinhausser@gmail.com>
 */
abstract class Compound extends Composite
{
    /** @var Constraint[] */
    public $constraints = [];
    public function __construct($options = null)
    {
        if (isset($options[$this->getCompositeOption()])) {
            throw new ConstraintDefinitionException(\sprintf('You can\'t redefine the "%s" option. Use the "%s::getConstraints()" method instead.', $this->getCompositeOption(), __CLASS__));
        }
        $this->constraints = $this->getConstraints($this->normalizeOptions($options));
        parent::__construct($options);
    }
    protected final function getCompositeOption() : string
    {
        return 'constraints';
    }
    public final function validatedBy() : string
    {
        return CompoundValidator::class;
    }
    /**
     * @return Constraint[]
     */
    protected abstract function getConstraints(array $options) : array;
}
