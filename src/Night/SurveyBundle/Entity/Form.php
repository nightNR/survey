<?php
/**
 * Created by PhpStorm.
 * User: nightnr
 * Date: 10.01.17
 * Time: 23:41
 */

namespace Night\SurveyBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class Form
 * @package Night\SurveyBundle\Entity
 * @ORM\Entity()
 * @ORM\Table(name="forms")
 */
class Form
{
    /**
     * @var string
     * @ORM\Id()
     * @ORM\Column(type="guid")
     * @ORM\GeneratedValue(strategy="UUID")
     */
    private $id;

    /**
     * @var Survey
     * @ORM\ManyToOne(targetEntity="Survey", inversedBy="forms")
     */
    private $survey;

    /**
     * @var integer
     * @ORM\Column(name="display_order", type="smallint", nullable=false, options={"default": 1, "unsigned": true})
     */
    private $order;

    /**
     * @var string
     * @ORM\Column(type="string", nullable=true)
     */
    private $topic;

    /**
     * @var string
     * @ORM\Column(type="text", nullable=true)
     */
    private $topText;

    /**
     * @var Question[]
     * @ORM\OneToMany(targetEntity="Night\SurveyBundle\Entity\Question", mappedBy="form", cascade={"persist"})
     * @ORM\OrderBy({"order": "ASC"})
     */
    private $questions;

    /**
     * @var boolean
     * @ORM\Column(type="boolean",  options={"default": false})
     */
    private $isScs = false;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->questions = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Get id
     *
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set topText
     *
     * @param string $topText
     * @return Form
     */
    public function setTopText($topText)
    {
        $this->topText = $topText;

        return $this;
    }

    /**
     * Get topText
     *
     * @return string 
     */
    public function getTopText()
    {
        return $this->topText;
    }

    /**
     * @return string
     */
    public function getTopic()
    {
        return $this->topic;
    }

    /**
     * @param string $topic
     */
    public function setTopic($topic)
    {
        $this->topic = $topic;
    }

    /**
     * Set survey
     *
     * @param \Night\SurveyBundle\Entity\Survey $survey
     * @return Form
     */
    public function setSurvey(\Night\SurveyBundle\Entity\Survey $survey = null)
    {
        $this->survey = $survey;

        return $this;
    }

    /**
     * Get survey
     *
     * @return \Night\SurveyBundle\Entity\Survey 
     */
    public function getSurvey()
    {
        return $this->survey;
    }

    /**
     * @return int
     */
    public function getOrder()
    {
        return $this->order;
    }

    /**
     * @param int $order
     */
    public function setOrder($order)
    {
        $this->order = $order;
    }

    /**
     * Add questions
     *
     * @param \Night\SurveyBundle\Entity\Question $question
     * @return Form
     */
    public function addQuestion(\Night\SurveyBundle\Entity\Question $question)
    {
        $this->questions[] = $question;
        if($question->getForm() !== $this) {
            $question->setForm($this);
        }
        return $this;
    }

    /**
     * Remove questions
     *
     * @param \Night\SurveyBundle\Entity\Question $questions
     */
    public function removeQuestion(\Night\SurveyBundle\Entity\Question $questions)
    {
        $this->questions->removeElement($questions);
    }

    /**
     * Get questions
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getQuestions()
    {
        return $this->questions;
    }

    /**
     * @return bool
     */
    public function isIsScs()
    {
        return $this->isScs;
    }

    /**
     * @param bool $isScs
     */
    public function setIsScs($isScs)
    {
        $this->isScs = $isScs;
    }

    public function createCopy()
    {
        $copy = new self();
        $copy->setOrder($this->getOrder());
        $copy->setTopic($this->getTopic());
        $copy->setTopText($this->getTopText());
        $copy->setIsScs($this->isIsScs());
        /** @var Question $question */
        foreach($this->questions as $question) {
            $copy->addQuestion($question->createCopy());
        }
        return $copy;
    }
}
