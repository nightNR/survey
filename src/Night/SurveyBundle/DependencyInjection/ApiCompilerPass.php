<?php
/**
 * Created by PhpStorm.
 * User: pbalaz
 * Date: 3/29/16
 * Time: 5:55 PM
 */

namespace Night\SurveyBundle\DependencyInjection;


use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class ApiCompilerPass implements CompilerPassInterface
{

    /**
     * You can modify the container here before it is dumped to PHP code.
     *
     * @param ContainerBuilder $container
     */
    public function process(ContainerBuilder $container)
    {
        $taggedServices = $container->findTaggedServiceIds('api.service');

        $serviceProvider = $container->findDefinition('api');

        foreach ($taggedServices as $id => $tags) {
            $serviceProvider->addMethodCall('registerService', [ new Reference($id) ]);
        }
    }
}