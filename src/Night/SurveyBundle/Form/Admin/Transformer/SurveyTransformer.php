<?php
/**
 * Created by PhpStorm.
 * User: pbalaz
 * Date: 7/4/17
 * Time: 5:59 PM
 */

namespace Night\SurveyBundle\Form\Admin\Transformer;

use Doctrine\ORM\EntityManagerInterface;
use Night\SurveyBundle\Entity\Survey;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

class SurveyTransformer implements DataTransformerInterface
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * SurveyTransformer constructor.
     *
     * @param $entityManager
     */
    public function __construct($entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @inheritdoc
     * @param Survey $value
     */
    public function transform($survey)
    {
        if(null === $survey) {
            return '';
        }
        return $survey->getId();
    }

    /**
     * @inheritdoc
     */
    public function reverseTransform($value)
    {
        if(!$value) {
            return null;
        }

        $survey = $this->entityManager->find(Survey::class, $value);

        if (null === $survey) {
            // causes a validation error
            // this message is not shown to the user
            // see the invalid_message option
            throw new TransformationFailedException(sprintf(
                'An survey with id "%s" does not exist!',
                $value
            ));
        }

        return $survey;
    }
}
