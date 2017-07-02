<?php

namespace Night\SurveyBundle;

use Night\SurveyBundle\DependencyInjection\ApiCompilerPass;
use Night\SurveyBundle\Strategy\InputTypeStrategy\InputTypeStrategyPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class NightSurveyBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        $container->addCompilerPass(new InputTypeStrategyPass());
        $container->addCompilerPass(new ApiCompilerPass());
    }
}
