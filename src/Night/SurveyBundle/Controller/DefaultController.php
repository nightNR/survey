<?php

namespace Night\SurveyBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{

    /**
     * @Route("/")
     */
    public function redirectAction()
    {
        return $this->redirectToRoute('form', [
            "surveyId" => "9845353e-db4a-11e6-bf26-cec0c932ce01",
            "page" => "1"
        ]);
    }

    /**
     * @Route("/survey/{surveyId}/{page}", requirements={"page": "\d+"}, name="form")
     */
    public function indexAction(Request $request, $surveyId, $page = 1)
    {
        $session = $request->getSession();
        if (!$session->isStarted()) {
            $session->start();
        }

        $surveyService = $this->container->get("night_survey.survey");
        $surveyDTO = $surveyService->getSurveyData($surveyId, $page);

        $form = $surveyDTO->getForm();

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            $sessionKey = $surveyDTO->getSurveyId() . "-" . $surveyDTO->getFormId();
            $session->set($sessionKey, $data);
            if ($surveyDTO->getCurrentPage() != $surveyDTO->getMaxPage()) {
                return $this->redirectToRoute('form', [
                    "surveyId" => $surveyDTO->getSurveyId(),
                    "page" => $surveyDTO->getCurrentPage() + 1
                ]);
            } else {
                $surveyService->save($surveyId);
                return $this->redirectToRoute('result', [
                    "surveyId" => $surveyDTO->getSurveyId(),
                    "id" => $session->getId()
                ]);
            }
        }

        return $this->render('NightSurveyBundle:Default:index.html.twig', [
            'surveyDTO' => $surveyDTO
        ]);
    }

    /**
     * @Route("result/{surveyId}/{id}", name="result")
     */
    public function resultAction(Request $request, $surveyId, $id)
    {
        return $this->render('@NightSurvey/layout/layout.html.twig');
    }
}
