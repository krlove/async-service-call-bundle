<?php

namespace Krlove\AsyncServiceCallBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\HttpKernel\Kernel;

/**
 * This is the class that validates and merges configuration from your app/config files.
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/configuration.html}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('krlove_async_service_call');

        $rootNode
            ->children()
                ->scalarNode('console_path')
                    ->defaultValue(Kernel::MAJOR_VERSION < 3 ? 'app/console' : 'bin/console')
                ->end()
                ->scalarNode('php_path')
                    ->defaultValue(null)
                ->end()
            ->end();

        return $treeBuilder;
    }
}
