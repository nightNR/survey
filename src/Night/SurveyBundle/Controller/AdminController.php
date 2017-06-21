<?php
/**
 * Created by PhpStorm.
 * User: pbalaz
 * Date: 6/21/17
 * Time: 8:23 PM
 */

namespace Night\SurveyBundle\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Night\SurveyBundle\Entity\Survey;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use UserBundle\Entity\User;

class AdminController extends Controller
{
    const ENTRIES_PER_PAGE = 10;

    /**
     * @Route("admin/survey/list/{page}", defaults={"page": 1})
     * @throws \LogicException
     */
    public function surveyListAction($page)
    {
        /** @var User $user */
        $user = $this->getUser();
        $surveys = $user->getSurveys();
        $surveySlice = $surveys->slice(max($page - 1, 0) * self::ENTRIES_PER_PAGE, self::ENTRIES_PER_PAGE);
        return $this->render('@NightSurvey/Admin/survey_list.html.twig', [
            'surveyAdminListDTO' => [
                'title' => 'administration.survey.list',
                'surveys' => $surveySlice,
                'page' => $page,
                'page_count' => ceil($surveys->count() / self::ENTRIES_PER_PAGE)
            ]
        ]);
    }

    /**
     * @param $surveyId
     *
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("admin/survey/edit/{surveyId}/{page}", defaults={"page": 1})
     */
    public function surveyEditAction($surveyId, $page)
    {
        /** @var EntityManagerInterface $em */
        $em = $this->get('doctrine.orm.entity_manager');
        $survey = $em->getRepository(Survey::class)->find($surveyId);
        $forms = $survey->getForms();
        $formSlice = $forms->slice(max($page - 1, 0) * self::ENTRIES_PER_PAGE, self::ENTRIES_PER_PAGE);
        return $this->render('@NightSurvey/Admin/survey_edit.html.twig', [
            'surveyAdminListDTO' => [
                'title' => $survey->getTitle(),
                'forms' => $formSlice,
                'page' => $page,
                'page_count' => ceil($forms->count() / self::ENTRIES_PER_PAGE),
                'survey_id' => $surveyId
            ]
        ]);
    }
}
