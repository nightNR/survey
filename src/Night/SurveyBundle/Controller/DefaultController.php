<?php

namespace Night\SurveyBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{
    /**
     * @Route("/survey/{surveyId}/{page}", requirements={"page": "\d+"}, name="form")
     */
    public function indexAction(Request $request, $surveyId, $page = 1)
    {
        $session = $request->getSession();
        if(!$session->isStarted()) {
            $session->start();
        }

        $surveyService = $this->container->get("night_survey.survey");
        $surveyDTO = $surveyService->getSurveyData($surveyId, $page);

        $form = $surveyDTO->getForm();

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            $sessionKey = $surveyDTO->getSurveyId() . "-" . $surveyDTO->getFormId();
            $session->set($sessionKey, $data);
            dump($session);
            return $this->redirectToRoute('form', [
                "surveyId" => $surveyDTO->getSurveyId(),
                "page" => $surveyDTO->getCurrentPage() + 1
            ]);
        }

        return $this->render('NightSurveyBundle:Default:index.html.twig', [
            'surveyDTO' => $surveyDTO
        ]);
    }
}
