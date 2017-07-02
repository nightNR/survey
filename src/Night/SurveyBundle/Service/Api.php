<?php
/**
 * Created by PhpStorm.
 * User: pbalaz
 * Date: 3/24/16
 * Time: 3:06 PM
 */

namespace Night\SurveyBundle\Service;


use Doctrine\ORM\EntityManager;
use Night\SurveyBundle\Service\ApiCommands\ApiServiceInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

class Api
{

    use ContainerAwareTrait;
    /**
     * @var EntityManager
     */
    private $em;

    /**
     * @var ApiServiceInterface[]
     */
    private $services = [];

    /**
     * Api constructor.
     * @param EntityManager $em
     */
    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    /**
     * @param ApiServiceInterface $service
     */
    public function registerService(ApiServiceInterface $service) {
        $this->services[$service->getName()] = $service;
    }

    /**
     * @param $serviceName
     *
     * @return ApiServiceInterface
     */
    public function getService($serviceName)
    {
        if (!$this->serviceExists($serviceName)) {
            throw new \InvalidArgumentException("Service $serviceName not exists!");
        }

        return $this->services[$serviceName];
    }

    /**
     * @param string $serviceName
     *
     * @return bool
     */
    public function serviceExists($serviceName)
    {
        return array_key_exists($serviceName, $this->services);
    }

    /**
     * @param $serviceName
     * @param $command
     * @param array $data
     * @return array
     */
    public function handleRequest($serviceName, $command, $data) {
        $data = $data?:[];
        $service = $this->getService($serviceName);
        $service->setContainer($this->container);
        $return = call_user_func_array([ $service, $command ], $data);
        $this->em->flush();
        return $return;
    }

    protected function handlerHasMethod(ApiServiceInterface $service, $method) {
        if(!method_exists($service, $method)) {
            throw new \InvalidArgumentException('Method ' . $method . ' does not exists in service ' . $service->getName());
        }

        return true;
    }

}