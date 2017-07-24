<?php
/**
 * Created by PhpStorm.
 * User: pbalaz
 * Date: 7/4/17
 * Time: 5:31 PM
 */

namespace Night\SurveyBundle\Service\ApiCommands;

use Night\SurveyBundle\Form\Admin\FormType;
use Symfony\Bridge\Twig\TwigEngine;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Translation\TranslatorInterface;

class Form extends AbstractApiService
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
        return 'form';
    }

    public function removeForm($data)
    {
        if($data === null || !array_key_exists('form_id', $data)) {
            return [
                'flashMessage' => [
                    'status' => 'error',
                    'message' => $this->translator->trans('administration.messages.error.form.not_found', [], 'NightSurveyBundle')
                ]
            ];
        }
        $survey = $this->em->find(\Night\SurveyBundle\Entity\Form::class, $data['form_id']);
        $this->em->remove($survey);
        $this->em->flush();
        return [
            'flashMessage' => [
                'status' => 'OK',
                'message' => $this->translator->trans('administration.messages.success.form.deleted', [], 'NightSurveyBundle')
            ],
            'reload' => 0
        ];
    }

    public function createOrEditForm($data = null)
    {
        $formEntity = new \Night\SurveyBundle\Entity\Form();
        $title = 'administration.form.create.survey';
        if($data !== null && array_key_exists('survey_id', $data) && $data['survey_id'] != null ) {
            $survey = $this->em->find(\Night\SurveyBundle\Entity\Survey::class, $data['survey_id']);
            $formEntity->setSurvey($survey);
        }
        if($data !== null && array_key_exists('form_id', $data) && $data['form_id'] != null ) {
            $formEntity = $this->em->find(\Night\SurveyBundle\Entity\Form::class, $data['form_id']);
            $title = 'administration.form.edit.form';
        }
        $form = $this->formFactory->create(FormType::class, $formEntity);
        $form->handleRequest($this->getRequest());
        if($form->isSubmitted() && $form->isValid()) {
            if(!$formEntity->getOrder()) {
                $formEntity->setOrder($formEntity->getSurvey()->getMaxOrderNumber()+1);
            }
            $this->em->persist($formEntity);
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
