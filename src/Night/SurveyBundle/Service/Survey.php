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
use Night\SurveyBundle\Entity\UniversalEnum;
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

    /** @var InputTypeStrategyInterface[] */
    protected $inputTypeStrategies = [];

    /** @var  SessionInterface */
    protected $session;

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
        if (!isset($this->inputTypeStrategies[$inputType])) {
            throw new \InvalidArgumentException(sprintf("Invalid input type provided - %s", $inputType));
        }
        return $this->inputTypeStrategies[$inputType];
    }

    public function getSurveyData($surveyId, $page)
    {
        $survey = $this->em->find(\Night\SurveyBundle\Entity\Survey::class, $surveyId);
        $currentForm = null;
        /** @var Form $form */
        foreach ($survey->getForms() as $form) {
            if ($form->getOrder() == $page) {
                $currentForm = $form;
                break;
            }
        }

        if ($currentForm === null) {
            throw new \InvalidArgumentException(sprintf("Survey page %s not exist.", $page));
        }

        $formView = $this->generateForm($currentForm);

        return new SurveyDTO($surveyId, $currentForm->getId(), $survey->getTitle(), $currentForm->getTopic(), (int)$page, $survey->getForms()->count(), $currentForm->getTopText(), $formView);
    }

    public function save($surveyId)
    {
        $survey = $this->em->getRepository(\Night\SurveyBundle\Entity\Survey::class)->find($surveyId);

        $dataHolder = new DataHolder();
        foreach ($survey->getForms() as $form) {
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
        if ($data === null) {
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

    public function getScsScore($survey, $id)
    {
        $scsNegativeMatrix = [
            'sj',
            'i',
            'oi',
        ];
        /** @var SubmittedData $result */
        $result = $this->em->find(SubmittedData::class, [
            'id' => $id,
            'survey' => $survey
        ]);
        $scsForm = $this->getScsForm();
        $totalScore = 0;
        $groupResults = [];
        $maxScore = 0;
        /** @var Question $question */
        foreach ($scsForm->getQuestions() as $question) {
            if(empty($question->getGroup())) {
                continue;
            }
            $resultValue = $result->getQuestionResult($question);
            $totalScore += $resultValue;
            $maxValue = $this->getQuestionMaxValue($question);
            $maxScore+=$maxValue;
            if(array_key_exists($question->getGroup(), $scsNegativeMatrix)) {
                $resultValue = $maxValue + 1 - $resultValue;
            }
            $groupResults[$question->getGroup()][] = $resultValue;
        }
        foreach($groupResults as $key => $result) {
            $groupResults[$key] = array_sum($result) / count($result);
        }
        return [
            'hsx' => array_sum($groupResults),
            'total_score' => $totalScore,
            'max_score' => $maxScore,
            'percent' => floor(($totalScore/$maxScore)*100)
        ];
    }

    /**
     * @param $survey
     * @return array
     */
    public function getResultAsCsv($survey)
    {
        /** @var SubmittedData[] $results */
        $results = $this->em->getRepository(SubmittedData::class)->findBy([
            'survey' => $survey
        ]);

        $output = [];
        foreach($results as $result) {
            $survey = $result->getSurvey();
            /** @var Form[] $forms */
            $forms = $survey->getForms();
            foreach($forms as $form) {
                /** @var Question $question */
                foreach($form->getQuestions() as $question) {
                    if(!array_key_exists($question->getId(), $output)) {
                        $output['name'][$question->getId()] = $question->getQuestionText();
                    }
                    $output[$result->getId()][$question->getId()] = $this->getQuestionResult($result, $question);
                }
            }
        }
        return $output;
    }

    private function getScsForm()
    {
        return current($this->em->getRepository(Form::class)->findBy([
            "id" => '9845353e-db4a-11e6-bf26-cec0c932ce03'
        ]));
    }

    private function getQuestionMaxValue(Question $question)
    {
        $enums = $question->getInputEnums();
        $maxValue = 0;
        /** @var UniversalEnum $enum */
        foreach($enums as $enum) {
            if($maxValue < $enum->getValue()) {
                $maxValue = $enum->getValue();
            }
        }
        return $maxValue;
    }

    private function getQuestionResult(SubmittedData $result, Question $question)
    {
        $resultData = $result->getQuestionResult($question);
        switch($question->getInputType()) {
            case "input_radio":
            case "input_choice":
                /** @var UniversalEnum $enum */
                foreach($question->getInputEnums() as $enum) {
                    if($enum->getValue() == $resultData) {
                        $resultData = $enum->getLabel();
                        break;
                    }
                }
                break;
        }
        return $resultData;
    }
}