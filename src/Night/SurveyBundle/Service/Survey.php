<?php
/**
 * Created by PhpStorm.
 * User: nightnr
 * Date: 15.01.17
 * Time: 18:43
 */

namespace Night\SurveyBundle\Service;


use Doctrine\ORM\EntityManagerInterface;
use Night\SurveyBundle\DTO\SurveyDTO;
use Night\SurveyBundle\Entity\DataHolder;
use Night\SurveyBundle\Entity\Form;
use Night\SurveyBundle\Entity\Question;
use Night\SurveyBundle\Entity\SubmittedData;
use Night\SurveyBundle\Strategy\InputTypeStrategy\InputTypeStrategyInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class Survey
{
    /**
     * @var EntityManagerInterface
     */
    protected $em;

    /** @var  FormFactoryInterface */
    protected $formFactory;

    /** @var InputTypeStrategyInterface[]  */
    protected $inputTypeStrategies = [];

    /** @var  SessionInterface */
    protected  $session;

    /**
     * Survey constructor.
     * @param EntityManagerInterface $em
     * @param FormFactoryInterface $formFactory
     */
    public function __construct(EntityManagerInterface $em, FormFactoryInterface $formFactory, SessionInterface $session)
    {
        $this->em = $em;
        $this->formFactory = $formFactory;
        $this->session = $session;
    }

    public function addInputTypeStrategy(InputTypeStrategyInterface $inputTypeStrategy)
    {
        $this->inputTypeStrategies[$inputTypeStrategy->getInputType()] = $inputTypeStrategy;
    }

    protected function getInputTypeStrategy($inputType)
    {
        if(!isset($this->inputTypeStrategies[$inputType])) {
            throw new \InvalidArgumentException(sprintf("Invalid input type provided - %s", $inputType));
        }
        return $this->inputTypeStrategies[$inputType];
    }

    public function getSurveyData($surveyId, $page)
    {
        $survey = $this->em->find(\Night\SurveyBundle\Entity\Survey::class, $surveyId);
        $currentForm = null;
        /** @var Form $form */
        foreach($survey->getForms() as $form){
            if($form->getOrder() == $page) {
                $currentForm = $form;
                break;
            }
        }

        if($currentForm === null) {
            throw new \InvalidArgumentException(sprintf("Survey page %s not exist.", $page));
        }

        $formView = $this->generateForm($currentForm);

        return new SurveyDTO($surveyId, $currentForm->getId(), $survey->getTitle(), $currentForm->getTopic(), (int)$page, $survey->getForms()->count(), $currentForm->getTopText(), $formView!==null?$formView:null);
    }

    public function save($surveyId)
    {
        $survey = $this->em->getRepository(\Night\SurveyBundle\Entity\Survey::class)->find($surveyId);

        $dataHolder = new DataHolder();
        foreach($survey->getForms() as $form) {
            $sessionKey = $this->getSessionKeyFromForm($form);
            $dataHolder->$sessionKey = $this->session->get($sessionKey);
        }

        $data = new SubmittedData();
        $data->setId($this->session->getId());
        $data->setSurvey($survey);
        $data->setData($dataHolder);

        $this->em->persist($data);
        $this->em->flush();
    }

    /**
     * @param Form $currentForm
     * @return \Symfony\Component\Form\FormInterface
     */
    private function generateForm(Form $currentForm)
    {
        $sessionKey = $this->getSessionKeyFromForm($currentForm);
        $data = $this->session->get($sessionKey);
        if($data === null) {
            $data = new DataHolder();
        }

        $formBuilder = $this->formFactory->createBuilder('Symfony\Component\Form\Extension\Core\Type\FormType', $data, [
            'attr' => [
                'id' => $currentForm->getId()
            ]
        ]);
        /** @var Question $question */
        foreach ($currentForm->getQuestions() as $question) {
            $formBuilder->add(
                $this->createQuestionField($question)
            );
        }
        return $formBuilder->getForm();
    }

    private function createQuestionField(Question $question)
    {
        return $this->getInputTypeStrategy($question->getInputType())->process($question);
    }

    private function getSessionKeyFromForm(Form $form)
    {
        return $form->getSurvey()->getId() . "-" . $form->getId();
    }

}