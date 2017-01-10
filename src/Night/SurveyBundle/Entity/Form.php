<?php
/**
 * Created by PhpStorm.
 * User: nightnr
 * Date: 10.01.17
 * Time: 23:41
 */

namespace Night\SurveyBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class Form
 * @package Night\SurveyBundle\Entity\Traits
 * @ORM\Entity()
 * @ORM\Table(name="forms")
 */
class Form
{
    /**
     * @var integer
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
     * @var string
     * @ORM\Column(type="text", nullable=true)
     */
    private $topText;

    /**
     * @var Question
     * @ORM\OneToMany(targetEntity="Night\SurveyBundle\Entity\Question", mappedBy="form")
     */
    private $questions;
}