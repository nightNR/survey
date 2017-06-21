<?php
/**
 * Created by PhpStorm.
 * User: pbalaz
 * Date: 10/27/16
 * Time: 1:46 PM
 */

namespace Night\HwiOAuthExtendBundle\Controller;

use HWI\Bundle\OAuthBundle\Controller\ConnectController as BaseController;

use HWI\Bundle\OAuthBundle\Security\Core\Authentication\Token\OAuthToken;
use Symfony\Bundle\FrameworkBundle\Translation\Translator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class ConnectController extends BaseController
{
    /**
     * Connects a user to a given account if the user is logged in and connect is enabled.
     *
     * @param Request $request The active request.
     * @param string $service Name of the resource owner to connect to.
     *
     * @throws \Exception
     *
     * @return Response
     *
     * @throws NotFoundHttpException if `connect` functionality was not enabled
     * @throws AccessDeniedException if no user is authenticated
     */
    public function connectServiceAction(Request $request, $service)
    {
        $connect = $this->container->getParameter('hwi_oauth.connect');
        if (!$connect) {
            throw new NotFoundHttpException();
        }

        $hasUser = $this->isGranted('IS_AUTHENTICATED_REMEMBERED');
        if (!$hasUser) {
            throw new AccessDeniedException('Cannot connect an account.');
        }

        // Get the data from the resource owner
        $resourceOwner = $this->getResourceOwnerByName($service);

        $session = $request->getSession();
        $key = $request->query->get('key', time());

        if ($resourceOwner->handles($request)) {
            $accessToken = $resourceOwner->getAccessToken(
                $request,
                $this->container->get('hwi_oauth.security.oauth_utils')->getServiceAuthUrl($request, $resourceOwner)
            );

            // save in session
            $session->set('_hwi_oauth.connect_confirmation.' . $key, $accessToken);
        } else {
            $accessToken = $session->get('_hwi_oauth.connect_confirmation.' . $key);
        }

        // Redirect to the login path if the token is empty (Eg. User cancelled auth)
        if (null === $accessToken) {
            return $this->redirectToRoute('hwi_oauth_service_redirect', ['service' => $service]);
        }

        $userInformation = $resourceOwner->getUserInformation($accessToken);

        /** @var $currentToken OAuthToken */
        $currentToken = $this->getToken();
        $currentUser = $currentToken->getUser();

        $this->container->get('hwi_oauth.account.connector')->connect($currentUser, $userInformation);

        if ($currentToken instanceof OAuthToken) {
            // Update user token with new details
            $newToken =
                is_array($accessToken) &&
                (isset($accessToken['access_token']) || isset($accessToken['oauth_token'])) ?
                    $accessToken : $currentToken->getRawToken();

            $this->authenticateUser($request, $currentUser, $service, $newToken, false);
        }

        /** @var Session $session */
        $session = $request->getSession();
        /** @var Translator $translator */
        $translator = $this->container->get('translator');
        $session->getFlashBag()->add('success', $translator->trans('security.connect.success', ["%service%" => ucfirst($service)], "AmonthiaBundle"));

        return $this->redirectToRoute('fos_user_profile_show');
    }
}