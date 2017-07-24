<?php
/**
 * Created by PhpStorm.
 * User: nightnr
 * Date: 17.01.17
 * Time: 19:59
 */

namespace Night\SurveyBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class Image
 * @package Night\SurveyBundle\Entity
 * @ORM\Entity()
 * @ORM\Table(name="images")
 */
class Image
{
    /**
     * @var string
     * @ORM\Id()
     * @ORM\OneToOne(targetEntity="Night\SurveyBundle\Entity\Question", inversedBy="image")
     */
    private $question;

    /**
     * @var string
     * @ORM\Column(name="path", type="string")
     */
    private $path;

    /**
     * @var string
     * @ORM\Column(name="alt", type="string")
     */
    private $alt;

    /**
     * @return string
     */
    public function getQuestion()
    {
        return $this->question;
    }

    /**
     * @param string $question
     */
    public function setQuestion($question)
    {
        $this->question = $question;
    }

    /**
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * @param string $path
     */
    public function setPath($path)
    {
        $this->path = $path;
    }

    /**
     * @return string
     */
    public function getAlt()
    {
        return $this->alt;
    }

    /**
     * @param string $alt
     */
    public function setAlt($alt)
    {
        $this->alt = $alt;
    }

    public function createCopy()
    {
        $copy = new self();
        $copy->setPath($this->getPath());
        $copy->setAlt($this->getAlt());
        return $copy;
    }
}