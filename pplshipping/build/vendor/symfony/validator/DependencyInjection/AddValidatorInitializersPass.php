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
use PPLShipping\Symfony\Component\DependencyInjection\ContainerBuilder;
use PPLShipping\Symfony\Component\DependencyInjection\Reference;
/**
 * @author Fabien Potencier <fabien@symfony.com>
 * @author Robin Chalas <robin.chalas@gmail.com>
 */
class AddValidatorInitializersPass implements CompilerPassInterface
{
    private $builderService;
    private $initializerTag;
    public function __construct(string $builderService = 'validator.builder', string $initializerTag = 'validator.initializer')
    {
        if (0 < \func_num_args()) {
            trigger_deprecation('symfony/validator', '5.3', 'Configuring "%s" is deprecated.', __CLASS__);
        }
        $this->builderService = $builderService;
        $this->initializerTag = $initializerTag;
    }
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition($this->builderService)) {
            return;
        }
        $initializers = [];
        foreach ($container->findTaggedServiceIds($this->initializerTag, \true) as $id => $attributes) {
            $initializers[] = new Reference($id);
        }
        $container->getDefinition($this->builderService)->addMethodCall('addObjectInitializers', [$initializers]);
    }
}
