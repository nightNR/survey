<?php
/**
 * Created by PhpStorm.
 * User: pbalaz
 * Date: 3/30/16
 * Time: 2:56 PM
 */

namespace Night\SurveyBundle\Service\ApiCommands;


use Doctrine\ORM\EntityManager;
use UserBundle\Entity\User;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

abstract class AbstractApiService implements ApiServiceInterface
{
    use ContainerAwareTrait;

    /**
     * @var EntityManager
     */
    protected $em;

    /**
     * ApiChapter constructor.
     * @param $em
     */
    public function __construct($em)
    {
        $this->em = $em;
    }

    abstract public function getName();

    /**
     * Get a user from the Security Token Storage.
     *
     * @return User|null
     *
     * @throws \LogicException If SecurityBundle is not available
     *
     * @see TokenInterface::getUser()
     */
    protected function getUser()
    {
        if (!$this->container->has('security.token_storage')) {
            throw new \LogicException('The SecurityBundle is not registered in your application.');
        }

        if (null === $token = $this->container->get('security.token_storage')->getToken()) {
            return;
        }

        if (!is_object($user = $token->getUser())) {
            // e.g. anonymous authentication
            return;
        }

        return $user;
    }
}