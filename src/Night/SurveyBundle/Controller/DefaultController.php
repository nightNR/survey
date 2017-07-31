<?php

namespace Night\SurveyBundle\Controller;

use Night\SurveyBundle\Service\Survey;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpFoundation\StreamedResponse;

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
        if (!$session->has("current_page")) {
            $session->set('current_page', 1);
        }
        if ($direction == "back" && $session->get("current_page") > 1) {
            $session->set('current_page', $session->get("current_page") - 1);
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
                $sessionId = $session->getId();
                $surveyService->save($surveyId);
                return $this->redirectToRoute('result', [
                    "surveyId" => $surveyDTO->getSurveyId(),
                    "id"       => $sessionId
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
        $answeredQuestions = $surveyService->getAnsweredQuestions($surveyId);
        return $this->render(
            '@NightSurvey/Default/result.html.twig',
            [
                'surveyDTO'         => $surveyDTO,
                'score'             => $scsScore,
                'answeredQuestions' => $answeredQuestions
            ]
        );
    }

    /**
     * @param $surveyId
     * @Route("export/{surveyId}", name="export", )
     *
     * @return StreamedResponse
     * @throws \LogicException
     */
    public function exportAction($surveyId)
    {
        /** @var Survey $surveyService */
        $surveyService = $this->container->get("night_survey.survey");
        $output = $surveyService->getResultAsCsv($surveyId);

        $phpExcel = $this->get('phpexcel');

        $excelObject = $phpExcel->createPHPExcelObject();
        $excelObject->getProperties()
            ->setCreator('Survey')
            ->setCreated()
            ->setTitle('Export for survey ' . $surveyId);
        $activeSheet = $excelObject->setActiveSheetIndex(0);
        $activeSheet->setTitle('Exported data');
        if(array_key_exists('name', $output)) {
            $questions = $output['name'];
            $row = 1;
            unset($output['name']);
            $columnNameMap = [];
            $column = 0;
            foreach($questions as $id => $question) {
                $activeSheet->setCellValueByColumnAndRow($column, $row, $question);
                $columnNameMap[$id] = $column;
                $column++;
            }
            $row++;
            foreach($output as $submittedData) {
                foreach($submittedData as $questionId => $answer) {
                    $activeSheet->setCellValueByColumnAndRow($columnNameMap[$questionId], $row, $answer);
                }
                $row++;
            }
        }

        $writer = $phpExcel->createWriter($excelObject, 'Excel5');
        $response = $phpExcel->createStreamedResponse($writer);
        $dispositionHeader = $response->headers->makeDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT,
            $surveyId.'.xls'
        );
        $response->headers->set('Content-Type', 'text/vnd.ms-excel; charset=utf-8');
        $response->headers->set('Pragma', 'public');
        $response->headers->set('Cache-Control', 'maxage=1');
        $response->headers->set('Content-Disposition', $dispositionHeader);

        return $response;
    }

    /**
     * @Route("email/{surveyId}/{id}", name="email")
     */
    public function emailAction(Request $request, $surveyId, $id)
    {
        $em = $this->container->get('doctrine.orm.default_entity_manager');
        /** @var \Night\SurveyBundle\Entity\Survey $survey */
        $survey = $em->getRepository(\Night\SurveyBundle\Entity\Survey::class)->find($surveyId);
        /** @var Survey $surveyService */
        $surveyService = $this->container->get("night_survey.survey");

        $scsScore = [
            'hsx' => 10,
            'total_score' => 10,
            'max_score' => 10,
            'percent' => floor((10/10)*100)
        ];

        $surveyService->sendEmail($survey, 'nightnr@gmail.com');

        return new Response($this->renderView(
            '@NightSurvey/Default/email.html.twig',
            [
                'score'     => $scsScore,
                'surveyId'  => $survey->getId(),
                'id'        => $id
            ]
        ));
    }
}
