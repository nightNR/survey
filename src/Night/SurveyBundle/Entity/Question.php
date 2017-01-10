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
     * @var integer
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
}