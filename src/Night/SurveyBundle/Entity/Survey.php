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
}