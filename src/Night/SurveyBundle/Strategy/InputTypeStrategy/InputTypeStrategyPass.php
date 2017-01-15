<?php
/**
 * Created by PhpStorm.
 * User: nightnr
 * Date: 15.01.17
 * Time: 21:09
 */

namespace Night\SurveyBundle\Strategy\InputTypeStrategy;


use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class InputTypeStrategyPass implements CompilerPassInterface
{

    /**
     * You can modify the container here before it is dumped to PHP code.
     *
     * @param ContainerBuilder $container
     */
    public function process(ContainerBuilder $container)
    {
        if(!$container->has("night_survey.survey")) {
            return;
        }

        $definition = $container->findDefinition('night_survey.survey');

        $taggedServices = $container->findTaggedServiceIds('night_survey.input_type_strategy');

        foreach($taggedServices as $id => $tags) {
            $definition->addMethodCall('addInputTypeStrategy', [new Reference($id)]);
        }
    }
}