<?php
/**
 * Created by PhpStorm.
 * User: pbalaz
 * Date: 10/20/16
 * Time: 1:04 PM
 */

namespace UserBundle\Service;

use Doctrine\ORM\EntityManagerInterface;
use FOS\UserBundle\Model\UserManagerInterface;
use HWI\Bundle\OAuthBundle\OAuth\Response\UserResponseInterface;
use HWI\Bundle\OAuthBundle\Security\Core\User\FOSUBUserProvider as BaseProvider;
use Symfony\Bundle\FrameworkBundle\Templating\Asset\PackageFactory;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use UserBundle\Entity\LoginFacebook;
use UserBundle\Entity\LoginGoogle;
use UserBundle\Entity\LoginInterface;
use UserBundle\Entity\OAuthUser;

class UserProvider extends BaseProvider
{
    protected $session;

    protected $em;

    public function __construct(UserManagerInterface $userManager, EntityManagerInterface $em, \Symfony\Component\HttpFoundation\Session\Session $session, array $properties)
    {
        $this->session = $session;
        $this->em = $em;
        parent::__construct($userManager, $properties);
    }

    /**
     * {@inheritDoc}
     */
    public function connect(UserInterface $user, UserResponseInterface $response)
    {
        //on connect - get the access token and the user ID
        $service = $response->getResourceOwner()->getName();

        $username = $response->getUsername();

        if($service == 'facebook') {
            $loginTypeClass = "UserBundle:LoginFacebook";
        } else {
            $loginTypeClass = "UserBundle:LoginGoogle";
        }

        /** @var LoginInterface $login */
        $login = $this->em->getRepository($loginTypeClass)->find($username);
        //we "disconnect" previously connected users
        if (null !== $login) {
            throw new \Exception(sprintf("Account already connected with %s", [$service]));
        }
        /** @var \UserBundle\Entity\User $user */
        //we connect current user
        if($service == 'facebook') {
            $loginFacebook = new LoginFacebook();
            $loginFacebook->setId($response->getUsername());
            $loginFacebook->setAccessToken($response->getAccessToken());
            $user->setFacebookLogin($loginFacebook);
        } else {
            $loginGoogle = new LoginGoogle();
            $loginGoogle->setId($response->getUsername());
            $loginGoogle->setAccessToken($response->getAccessToken());
            $user->setGoogleLogin($loginGoogle);
        }
        $this->userManager->updateUser($user);
    }
    /**
     * {@inheritdoc}
     */
    public function loadUserByOAuthUserResponse(UserResponseInterface $response)
    {
        $service = $response->getResourceOwner()->getName();
        $username = $response->getUsername();

        if($service == 'facebook') {
            $loginTypeClass = "UserBundle:LoginFacebook";
        } else {
            $loginTypeClass = "UserBundle:LoginGoogle";
        }

        /** @var LoginInterface $login */
        $login = $this->em->getRepository($loginTypeClass)->find($username);

        //when the user is registrating
        if (null === $login) {
            $this->session->set('oauth_user', (new OAuthUser($response))->asArray());

            return null;
        } else {
            $user = $login->getUser();
            $login->setId($username);
            $login->setAccessToken($response->getAccessToken());
            $this->userManager->updateUser($user);
            return $user;
        }
    }

}