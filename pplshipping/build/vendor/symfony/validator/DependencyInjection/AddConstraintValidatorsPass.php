<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace PPLShipping\Symfony\Component\Validator\DependencyInjection;

use PPLShipping\Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use PPLShipping\Symfony\Component\DependencyInjection\Compiler\ServiceLocatorTagPass;
use PPLShipping\Symfony\Component\DependencyInjection\ContainerBuilder;
use PPLShipping\Symfony\Component\DependencyInjection\Reference;
/**
 * @author Johannes M. Schmitt <schmittjoh@gmail.com>
 * @author Robin Chalas <robin.chalas@gmail.com>
 */
class AddConstraintValidatorsPass implements CompilerPassInterface
{
    private $validatorFactoryServiceId;
    private $constraintValidatorTag;
    public function __construct(string $validatorFactoryServiceId = 'validator.validator_factory', string $constraintValidatorTag = 'validator.constraint_validator')
    {
        if (0 < \func_num_args()) {
            trigger_deprecation('symfony/validator', '5.3', 'Configuring "%s" is deprecated.', __CLASS__);
        }
        $this->validatorFactoryServiceId = $validatorFactoryServiceId;
        $this->constraintValidatorTag = $constraintValidatorTag;
    }
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition($this->validatorFactoryServiceId)) {
            return;
        }
        $validators = [];
        foreach ($container->findTaggedServiceIds($this->constraintValidatorTag, \true) as $id => $attributes) {
            $definition = $container->getDefinition($id);
            if (isset($attributes[0]['alias'])) {
                $validators[$attributes[0]['alias']] = new Reference($id);
            }
            $validators[$definition->getClass()] = new Reference($id);
        }
        $container->getDefinition($this->validatorFactoryServiceId)->replaceArgument(0, ServiceLocatorTagPass::register($container, $validators));
    }
}
