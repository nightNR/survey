<?php
/**
 * Created by PhpStorm.
 * User: nightnr
 * Date: 10.01.17
 * Time: 23:59
 */

namespace Night\SurveyBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class UniversalEnum
 * @package Night\SurveyBundle\Entity
 * @ORM\Entity()
 * @ORM\Table(name="enums")
 */
class UniversalEnum
{
    /**
     * @var Question
     * @ORM\ManyToOne(targetEntity="Night\SurveyBundle\Entity\Question", inversedBy="inputEnums")
     */
    private $question;

    /**
     * @var string
     * @ORM\Id()
     * @ORM\Column(name="id", type="guid")
     * @ORM\GeneratedValue(strategy="UUID")
     */
    private $id;

    /**
     * @var integer
     * @ORM\Column(type="integer", nullable=false)
     */
    private $value;

    /**
     * @var string
     * @ORM\Column(type="string", nullable=true)
     */
    private $label;

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
     * Set value
     *
     * @param integer $value
     * @return UniversalEnum
     */
    public function setValue($value)
    {
        $this->value = $value;

        return $this;
    }

    /**
     * Get value
     *
     * @return integer 
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Set label
     *
     * @param string $label
     * @return UniversalEnum
     */
    public function setLabel($label)
    {
        $this->label = $label;

        return $this;
    }

    /**
     * Get label
     *
     * @return string 
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * Set question
     *
     * @param \Night\SurveyBundle\Entity\Question $question
     * @return UniversalEnum
     */
    public function setQuestion(\Night\SurveyBundle\Entity\Question $question = null)
    {
        $this->question = $question;

        return $this;
    }

    /**
     * Get question
     *
     * @return \Night\SurveyBundle\Entity\Question 
     */
    public function getQuestion()
    {
        return $this->question;
    }
}
