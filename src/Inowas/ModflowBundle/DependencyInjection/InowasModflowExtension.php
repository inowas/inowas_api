<?php

namespace Inowas\ModflowBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

/**
 * Class InowasModflowExtension
 * @package Inowas\ModflowBundle\DependencyInjection
 *
 * @codeCoverageIgnore
 */
class InowasModflowExtension extends Extension  implements ExtensionInterface
{
    /**
     * @param array $configs
     * @param ContainerBuilder $container
     * @return mixed
     */
    public function load(array $configs, ContainerBuilder $container)
    {

        $loader = new YamlFileLoader(
            $container,
            new FileLocator(__DIR__ . '/../Resources/config')
        );
        $loader->load('services.yml');

        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $container->setParameter('inowas.modflow.max_processes', $config['max_processes']);
        $container->setParameter('inowas.modflow.data_folder', $config['data_folder'].'/modflow');
        $container->setParameter('inowas.temp_folder', $config['temp_folder']);
        $container->setParameter('inowas.api_base_url', $config['api_base_url']);
    }
}