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
 * Injects the automapping configuration as last argument of loaders tagged with the "validator.auto_mapper" tag.
 *
 * @author Kévin Dunglas <dunglas@gmail.com>
 */
class AddAutoMappingConfigurationPass implements CompilerPassInterface
{
    private $validatorBuilderService;
    private $tag;
    public function __construct(string $validatorBuilderService = 'validator.builder', string $tag = 'validator.auto_mapper')
    {
        if (0 < \func_num_args()) {
            trigger_deprecation('symfony/validator', '5.3', 'Configuring "%s" is deprecated.', __CLASS__);
        }
        $this->validatorBuilderService = $validatorBuilderService;
        $this->tag = $tag;
    }
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasParameter('validator.auto_mapping') || !$container->hasDefinition($this->validatorBuilderService)) {
            return;
        }
        $config = $container->getParameter('validator.auto_mapping');
        $globalNamespaces = [];
        $servicesToNamespaces = [];
        foreach ($config as $namespace => $value) {
            if ([] === $value['services']) {
                $globalNamespaces[] = $namespace;
                continue;
            }
            foreach ($value['services'] as $service) {
                $servicesToNamespaces[$service][] = $namespace;
            }
        }
        $validatorBuilder = $container->getDefinition($this->validatorBuilderService);
        foreach ($container->findTaggedServiceIds($this->tag) as $id => $tags) {
            $regexp = $this->getRegexp(\array_merge($globalNamespaces, $servicesToNamespaces[$id] ?? []));
            $validatorBuilder->addMethodCall('addLoader', [new Reference($id)]);
            $container->getDefinition($id)->setArgument('$classValidatorRegexp', $regexp);
        }
        $container->getParameterBag()->remove('validator.auto_mapping');
    }
    /**
     * Builds a regexp to check if a class is auto-mapped.
     */
    private function getRegexp(array $patterns) : ?string
    {
        if (!$patterns) {
            return null;
        }
        $regexps = [];
        foreach ($patterns as $pattern) {
            // Escape namespace
            $regex = \preg_quote(\ltrim($pattern, '\\'));
            // Wildcards * and **
            $regex = \strtr($regex, ['\\*\\*' => '.*?', '\\*' => '[^\\\\]*?']);
            // If this class does not end by a slash, anchor the end
            if (!\str_ends_with($regex, '\\')) {
                $regex .= '$';
            }
            $regexps[] = '^' . $regex;
        }
        return \sprintf('{%s}', \implode('|', $regexps));
    }
}
