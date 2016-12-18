<?php

namespace Inowas\PyprocessingBundle\DependencyInjection;


use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * Class Configuration
 * @package Inowas\PyProcessingBundle\DependencyInjection
 *
 * @codeCoverageIgnore
 */
class Configuration implements ConfigurationInterface
{
    /**
     * @return TreeBuilder
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('inowas_pyprocessing');

        $rootNode
            ->children()
                ->scalarNode('prefix')
                    ->defaultValue('python')
                    ->cannotBeEmpty()
                ->end()
            ->end()
            ->children()
                ->integerNode('max_processes')
                    ->defaultValue(5)
                ->end()
            ->end()
            ->children()
                ->scalarNode('data_folder')
                    ->isRequired()
                    ->cannotBeEmpty()
                ->end()
            ->end()
            ->children()
                ->scalarNode('api_port')
                    ->isRequired()
                    ->cannotBeEmpty()
                ->end()
            ->end()
                ->children()
                    ->scalarNode('temp_folder')
                    ->isRequired()
                    ->cannotBeEmpty()
                ->end()
            ->end()
            ->children()
                ->scalarNode('pyprocessing_folder')
                    ->isRequired()
                    ->cannotBeEmpty()
                ->end()
            ->end()
            ->children()
                ->scalarNode('api_base_url')
                ->defaultValue('http://localhost')
                ->cannotBeEmpty()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
