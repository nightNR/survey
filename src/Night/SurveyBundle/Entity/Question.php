<?php
/**
 * Created by PhpStorm.
 * User: nightnr
 * Date: 10.01.17
 * Time: 23:49
 */

namespace Night\SurveyBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class Question
 * @package Night\SurveyBundle\Entity
 * @ORM\Entity()
 * @ORM\Table(name="questions")
 */
class Question
{

    /**
     * @var string
     * @ORM\Id()
     * @ORM\Column(name="id", type="guid")
     * @ORM\GeneratedValue(strategy="UUID")
     */
    private $id;

    /**
     * @var Form
     * @ORM\ManyToOne(targetEntity="Night\SurveyBundle\Entity\Form", inversedBy="questions")
     */
    private $form;

    /**
     * @var string
     * @ORM\Column(name="question_text", type="string")
     */
    private $questionText;

    /**
     * @var string
     * @ORM\Column(name="input_type", type="string", nullable=false)
     */
    private $inputType;

    /**
     * @var string
     * @ORM\OneToMany(targetEntity="Night\SurveyBundle\Entity\UniversalEnum", mappedBy="question")
     */
    private $inputEnums;

    /**
     * @var Image
     * @ORM\OneToOne(targetEntity="Night\SurveyBundle\Entity\Image", mappedBy="question")
     */
    private $image;

    /**
     * @var boolean
     * @ORM\Column(type="boolean", nullable=false, options={"default":true})
     */
    private $isRequired;

    /**
     * @var int
     * @ORM\Column(type="integer", nullable=false, options={"unsigned": true})
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $order;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->inputEnums = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set questionText
     *
     * @param string $questionText
     * @return Question
     */
    public function setQuestionText($questionText)
    {
        $this->questionText = $questionText;

        return $this;
    }

    /**
     * Get questionText
     *
     * @return string 
     */
    public function getQuestionText()
    {
        return $this->questionText;
    }

    /**
     * Set inputType
     *
     * @param string $inputType
     * @return Question
     */
    public function setInputType($inputType)
    {
        $this->inputType = $inputType;

        return $this;
    }

    /**
     * Get inputType
     *
     * @return string 
     */
    public function getInputType()
    {
        return $this->inputType;
    }

    /**
     * Set form
     *
     * @param \Night\SurveyBundle\Entity\Form $form
     * @return Question
     */
    public function setForm(\Night\SurveyBundle\Entity\Form $form = null)
    {
        $this->form = $form;

        return $this;
    }

    /**
     * Get form
     *
     * @return \Night\SurveyBundle\Entity\Form 
     */
    public function getForm()
    {
        return $this->form;
    }

    /**
     * Add inputEnums
     *
     * @param \Night\SurveyBundle\Entity\UniversalEnum $inputEnums
     * @return Question
     */
    public function addInputEnum(\Night\SurveyBundle\Entity\UniversalEnum $inputEnums)
    {
        $this->inputEnums[] = $inputEnums;

        return $this;
    }

    /**
     * Remove inputEnums
     *
     * @param \Night\SurveyBundle\Entity\UniversalEnum $inputEnums
     */
    public function removeInputEnum(\Night\SurveyBundle\Entity\UniversalEnum $inputEnums)
    {
        $this->inputEnums->removeElement($inputEnums);
    }

    /**
     * Get inputEnums
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getInputEnums()
    {
        return $this->inputEnums;
    }

    /**
     * @return Image
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * @param $image
     */
    public function setImage(Image $image)
    {
        $this->image = $image;
    }


    /**
     * @return bool
     */
    public function isIsRequired()
    {
        return $this->isRequired;
    }

    /**
     * @param bool $isRequired
     */
    public function setIsRequired($isRequired)
    {
        $this->isRequired = $isRequired;
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
}
