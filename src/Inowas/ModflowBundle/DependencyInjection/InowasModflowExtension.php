<?php

namespace Inowas\ModflowBundle\DependencyInjection;


use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;

class InowasModflowExtension extends Extension  implements ExtensionInterface
{
    /**
     * @param array $configs
     * @param ContainerBuilder $container
     * @return mixed
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        
    }

}