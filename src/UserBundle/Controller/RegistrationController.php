<?php
/**
 * Created by PhpStorm.
 * User: pbalaz
 * Date: 10/25/16
 * Time: 3:04 PM
 */

namespace UserBundle\Controller;

use FOS\UserBundle\Controller\RegistrationController as BaseController;
use FOS\UserBundle\Model\UserInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use UserBundle\Entity\LoginFacebook;
use UserBundle\Entity\LoginGoogle;
use UserBundle\Entity\User;

class RegistrationController extends BaseController
{
    public function registerAction()
    {
        $form = $this->container->get('fos_user.registration.form');
        $formHandler = $this->container->get('fos_user.registration.form.handler');
        $confirmationEnabled = $this->container->getParameter('fos_user.registration.confirmation.enabled');

        /** @var Session $session */
        $session = $this->container->get('session');
        $oauthResponse = $session->get('oauth_user');

        $process = $formHandler->process($confirmationEnabled);
        if($oauthResponse !== null && !$form->isSubmitted()) {
            $form->setData($this->createOAuthUser($form->getData(), $oauthResponse));
        }
        if ($process) {
            $user = $form->getData();

            $authUser = false;
            if ($confirmationEnabled) {
                $this->container->get('session')->set('fos_user_send_confirmation_email/email', $user->getEmail());
                $route = 'fos_user_registration_check_email';
            } else {
                $authUser = true;
                $route = 'fos_user_registration_confirmed';
            }

            $this->setFlash('fos_user_success', 'registration.flash.user_created');
            $url = $this->container->get('router')->generate($route);
            $response = new RedirectResponse($url);

            if ($authUser) {
                $this->authenticateUser($user, $response);
            }
            $session->remove('oauth_user');

            return $response;
        }

        return $this->container->get('templating')->renderResponse('FOSUserBundle:Registration:register.html.'.$this->getEngine(), array(
            'form' => $form->createView(),
        ));
    }

    private function createOAuthUser(User $user, $oauthResponse)
    {
        $user->setEmail($oauthResponse['email']);
        if(!empty($oauthResponse['facebook_id'])) {
            $loginFacebook = new LoginFacebook();
            $loginFacebook->setId($oauthResponse['facebook_id']);
            $loginFacebook->setAccessToken($oauthResponse['facebook_access_token']);
            $user->setFacebookLogin($loginFacebook);
        }

        if(!empty($oauthResponse['google_id'])) {
            $loginGoogle = new LoginGoogle();
            $loginGoogle->setId($oauthResponse['google_id']);
            $loginGoogle->setAccessToken($oauthResponse['google_access_token']);
            $user->setGoogleLogin($loginGoogle);
        }
        $user->setUsername($oauthResponse['name']);
        return $user;
    }
}