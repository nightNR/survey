<?php
/**
 * Created by PhpStorm.
 * User: pbalaz
 * Date: 3/24/16
 * Time: 1:18 PM
 */

namespace Night\SurveyBundle\Controller;


use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ApiController extends Controller
{
    /**
     *
     * Format: {type: (string)"/api method/", data: /data object/}
     *
     * @Route("/api/request", name="Api_request", defaults={"_format": "json"})
     * @Method("POST")
     *
     * @param Request $request
     * @return Response
     */
    public function requestAction(Request $request) {
        $apiService = $this->get('api');
        $apiService->setContainer($this->container);
        $serializer = $this->get('jms_serializer');

        return new Response($serializer->serialize($apiService->handleRequest($request->get('service'),$request->get('command') , $request->get('data')), 'json'));
    }
}