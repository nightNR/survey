<?php

namespace Night\SurveyBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{
    /**
     * @Route("/survey/{page}", requirements={"page": "\d+"}, name="form")
     */
    public function indexAction(Request $request, $page = 1)
    {
        $session = $request->getSession();
        if(!$session->isStarted()) {
            $session->start();
        }
        $maxPage = 8;
        dump($session->getId());
        dump($page);
        return $this->render('NightSurveyBundle:Default:index.html.twig', [
            'current_page' => $page,
            'max_page'  => $maxPage,
            'progress' => ceil(($page/$maxPage)*100)
        ]);
    }
}
