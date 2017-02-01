<?php

namespace Night\SurveyBundle\Controller;

use Night\SurveyBundle\Service\Survey;
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
            "surveyId" => "9845353e-db4a-11e6-bf26-cec0c932ce01"
        ]);
    }

    /**
     * @Route("/survey/{surveyId}/{direction}", name="form")
     */
    public function indexAction(Request $request, $surveyId, $direction = null)
    {
        $session = $request->getSession();
        if (!$session->isStarted()) {
            $session->start();
        }
        if(!$session->has("current_page")) {
            $session->set('current_page', 1);
        }
        if($direction == "back" && $session->get("current_page") > 1) {
            $session->set('current_page', $session->get("current_page")-1);
            return $this->redirectToRoute('form', [
                "surveyId" => $surveyId
            ]);
        }

        $surveyService = $this->container->get("night_survey.survey");
        $surveyDTO = $surveyService->getSurveyData($surveyId, $session->get("current_page"));

        $form = $surveyDTO->getForm();

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            $sessionKey = $surveyDTO->getSurveyId() . "-" . $surveyDTO->getFormId();
            $session->set($sessionKey, $data);
            if ($surveyDTO->getCurrentPage() != $surveyDTO->getMaxPage()) {
                $session->set('current_page', $surveyDTO->getCurrentPage() + 1);
                return $this->redirectToRoute('form', [
                    "surveyId" => $surveyDTO->getSurveyId()
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
        /** @var Survey $surveyService */
        $surveyService = $this->container->get("night_survey.survey");
        $surveyDTO = $surveyService->getSurveyData($surveyId, 1);
        $scsScore = $surveyService->getScsScore($surveyId, $id);
        return $this->render(
            '@NightSurvey/Default/result.html.twig',
            [
                'surveyDTO' => $surveyDTO,
                'score' => $scsScore
            ]
            );
    }
}
