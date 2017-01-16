<?php
/**
 * Created by PhpStorm.
 * User: nightnr
 * Date: 16.01.17
 * Time: 18:51
 */

namespace Night\SurveyBundle\Strategy\InputTypeStrategy;


use Night\SurveyBundle\Entity\Question;
use Night\SurveyBundle\Entity\UniversalEnum;
use Night\SurveyBundle\Form\InputType\RangeInput;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

class InputRadioRangeStrategy implements InputTypeStrategyInterface
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
        return "input_radio_range";
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
            RangeInput::class,
            null,
            [
                "label" => $question->getQuestionText(),
                "required" => true,
                "choices" => $optionsChoices,
                "constraints" => $constraints,
            ]
        );
    }
}