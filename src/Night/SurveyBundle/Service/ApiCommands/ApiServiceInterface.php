<?php
/**
 * Created by PhpStorm.
 * User: pbalaz
 * Date: 3/29/16
 * Time: 6:02 PM
 */

namespace Night\SurveyBundle\Service\ApiCommands;


use Symfony\Component\DependencyInjection\ContainerInterface;

interface ApiServiceInterface
{
    public function getName();

    public function setContainer(ContainerInterface $container = null);
}