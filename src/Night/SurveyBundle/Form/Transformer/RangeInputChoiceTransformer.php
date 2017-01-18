<?php
/**
 * Created by PhpStorm.
 * User: nightnr
 * Date: 17.01.17
 * Time: 22:22
 */

namespace Night\SurveyBundle\Form\Transformer;


use Symfony\Component\Form\ChoiceList\ChoiceListInterface;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

class RangeInputChoiceTransformer implements DataTransformerInterface
{

    private $choiceList;

    /**
     * Constructor.
     *
     * @param ChoiceListInterface $choiceList
     */
    public function __construct(ChoiceListInterface $choiceList)
    {
        $this->choiceList = $choiceList;
    }

    public function transform($choice)
    {
        return (string) $choice;
    }

    public function reverseTransform($value)
    {
        if (null !== $value && !is_string($value)) {
            throw new TransformationFailedException('Expected a string or null.');
        }
        $choices = $this->choiceList->getChoices();
        $tmp = [];
        $tmp[] = array_shift($choices);
        $tmp[] = array_shift($choices);
        $minChoice = $tmp[0]<$tmp[1]?$tmp[0]:$tmp[1];
        $maxChoice = $tmp[0]>$tmp[1]?$tmp[0]:$tmp[1];

        if($value >= $minChoice && $value <= $maxChoice){
            return (int) $value;
        }

        throw new TransformationFailedException(sprintf('Value %s is out of range %s - %s', $value, $minChoice, $maxChoice));
    }
}