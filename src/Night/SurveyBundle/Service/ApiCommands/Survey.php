<?php
/**
 * Created by PhpStorm.
 * User: nightnr
 * Date: 02.07.17
 * Time: 22:42
 */

namespace Night\SurveyBundle\Service\ApiCommands;


use Night\SurveyBundle\Entity\Form;
use Night\SurveyBundle\Form\Admin\SurveyType;
use Symfony\Bridge\Twig\TwigEngine;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Templating\EngineInterface;
use Symfony\Component\Translation\TranslatorInterface;

class Survey extends AbstractApiService
{
    /**
     * @var TranslatorInterface
     */
    protected $translator;

    /**
     * @var FormFactoryInterface
     */
    protected $formFactory;

    /**
     * @var TwigEngine
     */
    protected $templatingEngine;

    public function __construct($em, TranslatorInterface $translator, FormFactoryInterface $formFactory, TwigEngine $templatingEngine)
    {
        parent::__construct($em);
        $this->translator = $translator;
        $this->formFactory = $formFactory;
        $this->templatingEngine = $templatingEngine;
    }

    public function getName()
    {
        return 'survey';
    }

    public function reorderForms($data)
    {
        foreach($data as $form) {
            $formEntity = $this->em->getRepository(Form::class)->findOneBy([
                'id' => $form['id']
            ]);
            $formEntity->setOrder($form['order']);
            $this->em->persist($formEntity);
        }
        $this->em->flush();
        return [
            'flashMessage' => [
                'status' => 'OK',
                'message' => $this->translator->trans('administration.messages.change_order.ok', [], 'NightSurveyBundle')
            ]
        ];
    }

    public function removeSurvey($data)
    {
        if($data === null || !array_key_exists('survey_id', $data)) {
            return [
                'flashMessage' => [
                    'status' => 'error',
                    'message' => $this->translator->trans('administration.messages.error.survey.not_found', [], 'NightSurveyBundle')
                ]
            ];
        }
        $survey = $this->em->find(\Night\SurveyBundle\Entity\Survey::class, $data['survey_id']);
        $this->em->remove($survey);
        $this->em->flush();
        return [
            'flashMessage' => [
                'status' => 'OK',
                'message' => $this->translator->trans('administration.messages.success.survey.deleted', [], 'NightSurveyBundle')
            ],
            'reload' => 0
        ];
    }

    public function createOrEditSurvey($data = null)
    {
        $survey = new \Night\SurveyBundle\Entity\Survey();
        $title = 'administration.form.create.survey';
        if($data !== null && array_key_exists('survey_id', $data) && $data['survey_id'] != null ) {
            $survey = $this->em->find(\Night\SurveyBundle\Entity\Survey::class, $data['survey_id']);
            $title = 'administration.form.edit.survey';
        }
        $form = $this->formFactory->create(SurveyType::class, $survey);
        $form->handleRequest($this->getRequest());
        if($form->isSubmitted() && $form->isValid()) {
            $survey->setOwner($this->getUser());
            $this->em->persist($survey);
            $this->em->flush();
            return [
                'flashMessage' => [
                    'status' => 'OK',
                    'message' => $this->translator->trans('administration.messages.created.survey', [], 'NightSurveyBundle')
                ],
                'closeModal' => 0,
                'reload' => 2000
            ];
        }
        return [
            'form' => $this->templatingEngine->render('@NightSurvey/Admin/Form/form.html.twig', ['form' => $form->createView(), 'title' => $title])
        ];
    }
}