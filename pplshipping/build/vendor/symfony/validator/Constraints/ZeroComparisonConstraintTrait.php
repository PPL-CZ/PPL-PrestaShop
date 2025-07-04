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

use PPLShipping\Symfony\Component\Validator\Exception\ConstraintDefinitionException;
/**
 * @internal
 *
 * @author Jan Schädlich <jan.schaedlich@sensiolabs.de>
 * @author Alexander M. Turek <me@derrabus.de>
 */
trait ZeroComparisonConstraintTrait
{
    public function __construct(?array $options = null, ?string $message = null, ?array $groups = null, $payload = null)
    {
        if (null === $options) {
            $options = [];
        }
        if (isset($options['propertyPath'])) {
            throw new ConstraintDefinitionException(\sprintf('The "propertyPath" option of the "%s" constraint cannot be set.', static::class));
        }
        if (isset($options['value'])) {
            throw new ConstraintDefinitionException(\sprintf('The "value" option of the "%s" constraint cannot be set.', static::class));
        }
        parent::__construct(0, null, $message, $groups, $payload, $options);
    }
    public function validatedBy() : string
    {
        return parent::class . 'Validator';
    }
}
