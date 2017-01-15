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
     */
    private $forms;
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->forms = new \Doctrine\Common\Collections\ArrayCollection();
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
}
