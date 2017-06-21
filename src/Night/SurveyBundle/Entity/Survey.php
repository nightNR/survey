<?php
/**
 * Created by PhpStorm.
 * User: nightnr
 * Date: 10.01.17
 * Time: 23:31
 */

namespace Night\SurveyBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use UserBundle\Entity\User;

/**
 * Class Form
 * @package Night\SurveyBundle\Entity
 *
 * @ORM\Entity()
 * @ORM\Table(name="surveys")
 * @ORM\HasLifecycleCallbacks()
 */
class Survey
{
    /**
     * @var string
     * @ORM\Column(type="guid")
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="UUID")
     */
    private $id;

    /**
     * @var string
     * @ORM\Column(type="string", length=255, nullable=false)
     */
    private $title;

    /**
     * @var ArrayCollection<Form>
     * @ORM\OneToMany(targetEntity="Form", mappedBy="survey")
     * @ORM\OrderBy(value={"order": "ASC"})
     */
    private $forms;

    /**
     * @var ArrayCollection<SubmittedData>
     * @ORM\OneToMany(targetEntity="Night\SurveyBundle\Entity\SubmittedData", mappedBy="survey")
     */
    private $submittedData;

    /**
     * @var Question
     * @ORM\ManyToOne(targetEntity="Night\SurveyBundle\Entity\Question")
     */
    private $resultTargetQuestion;

    /**
     * @var User
     * @ORM\ManyToOne(targetEntity="UserBundle\Entity\User", inversedBy="surveys")
     */
    private $owner;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->forms = new \Doctrine\Common\Collections\ArrayCollection();
        $this->submittedData = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set title
     *
     * @param string $title
     * @return Survey
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string 
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Add forms
     *
     * @param \Night\SurveyBundle\Entity\Form $forms
     * @return Survey
     */
    public function addForm(\Night\SurveyBundle\Entity\Form $forms)
    {
        $this->forms[] = $forms;

        return $this;
    }

    /**
     * Remove forms
     *
     * @param \Night\SurveyBundle\Entity\Form $forms
     */
    public function removeForm(\Night\SurveyBundle\Entity\Form $forms)
    {
        $this->forms->removeElement($forms);
    }

    /**
     * Get forms
     *
     * @return \Doctrine\Common\Collections\Collection<Form>
     */
    public function getForms()
    {
        return $this->forms;
    }

    /**
     * @return ArrayCollection
     */
    public function getSubmittedData()
    {
        return $this->submittedData;
    }

    /**
     * @param SubmittedData $submittedData
     */
    public function addSubmittedData($submittedData)
    {
        $this->submittedData[] = $submittedData;
    }

    /**
     * @return Question
     */
    public function getResultTargetQuestion()
    {
        return $this->resultTargetQuestion;
    }

    /**
     * @param Question $resultTargetQuestion
     */
    public function setResultTargetQuestion($resultTargetQuestion)
    {
        $this->resultTargetQuestion = $resultTargetQuestion;
    }

    public function getOwner()
    {
        return $this->owner;
    }

    public function setOwner(User $user)
    {
        $this->owner = $user;
        if(!$user->hasSurvey($this)){
            $user->addSurvey($this);
        }
    }
}
