<?php
/**
 * Created by PhpStorm.
 * User: nightnr
 * Date: 15.01.17
 * Time: 21:20
 */

namespace Night\SurveyBundle\Strategy\InputTypeStrategy;


use Night\SurveyBundle\Entity\Question;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

class InputTextStrategy implements InputTypeStrategyInterface
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
        return "input_text";
    }

    public function process(Question $question)
    {
        $constraints = [];
        if($question->isIsRequired()){
            $constraints[] = new NotBlank();
        }
        return $this->formFactory->createNamedBuilder(
            $question->getId(),
            TextType::class,
            null,
            [
                "label" => $question->getQuestionText(),
                "constraints" => $constraints,
                "image" => $question->getImage()
            ]
        );
    }
}