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
     * @var integer
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
}