<?php
/**
 * Created by PhpStorm.
 * User: nightnr
 * Date: 15.01.17
 * Time: 21:23
 */

namespace Night\SurveyBundle\Strategy\InputTypeStrategy;


use Night\SurveyBundle\Entity\Question;
use Night\SurveyBundle\Entity\UniversalEnum;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

class InputChoiceStrategy implements InputTypeStrategyInterface
{
    /** @var  FormFactoryInterface */
    protected $formFactory;

    /**
     * InputTextStrategy constructor.
     * @param FormFactoryInterface $formFactory
     */
    public function __construct(FormFactoryInterface $formFactory)
    {
        $this->formFactory = $formFactory;
    }

    public function getInputType()
    {
        return "input_choice";
    }

    public function process(Question $question)
    {
        $constraints = [];
        if($question->isIsRequired()){
            $constraints[] = new NotBlank();
        }
        $choices = $question->getInputEnums();
        $optionsChoices = [];
        /** @var UniversalEnum $choice */
        foreach($choices as $choice) {
            $optionsChoices[$choice->getValue()] = $choice->getLabel();
        }
        return $this->formFactory->createNamedBuilder(
            $question->getId(),
            ChoiceType::class,
            null,
            [
                "label" => $question->getQuestionText(),
                "required" => true,
                "choices" => $optionsChoices,
                "choices_as_values" => false,
                "constraints" => $constraints
            ]
        );
    }
}