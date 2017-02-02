<?php
/**
 * Created by PhpStorm.
 * User: nightnr
 * Date: 18.01.17
 * Time: 00:56
 */

namespace Night\SurveyBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class SubmitedData
 * @package Night\SurveyBundle\Entity
 * @ORM\Entity()
 * @ORM\Table(name="submitted_data")
 */
class SubmittedData
{
    /**
     * @var string
     * @ORM\Column(name="id", nullable=false, type="string")
     * @ORM\Id()
     */
    private $id;

    /**
     * @var Survey
     * @ORM\ManyToOne(targetEntity="Night\SurveyBundle\Entity\Survey", inversedBy="submittedData")
     * @ORM\Id()
     */
    private $survey;

    /**
     * @var string
     * @ORM\Column(name="data", nullable=false, type="object")
     */
    private $data;

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param string $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return Survey
     */
    public function getSurvey()
    {
        return $this->survey;
    }

    /**
     * @param Survey $survey
     */
    public function setSurvey($survey)
    {
        $this->survey = $survey;
        $survey->addSubmittedData($this);
    }

    /**
     * @return string
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @param string $data
     */
    public function setData($data)
    {
        $this->data = $data;
    }

    public function getQuestionResult(Question $question)
    {
        /** @var DataHolder $formDataHolder */
        $formDataHolder = $this->data[$this->getFormId($question->getForm())];
        return $formDataHolder[$question->getId()];
    }

    public function getFormId(Form $form)
    {
        return $this->survey->getId() . "-" . $form->getId();
    }
}