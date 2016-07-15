<?php

namespace Inowas\PythonProcessBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * Class Configuration
 * @package Inowas\PythonProcessBundle\DependencyInjection
 *
 * @codeCoverageIgnore
 */
class Configuration implements ConfigurationInterface
{

    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('inowas_python_process');

        $rootNode
            ->children()
                ->scalarNode('prefix')
                    ->defaultValue('python')
                    ->cannotBeEmpty()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}