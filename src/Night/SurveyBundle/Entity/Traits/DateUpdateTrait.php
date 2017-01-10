<?php
/**
 * Created by PhpStorm.
 * User: nightnr
 * Date: 10.01.17
 * Time: 23:34
 */

namespace Night\SurveyBundle\Entity\Traits;

use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\Mapping as ORM;

trait DateUpdateTrait
{
    /**
     * @var \DateTime
     * @ORM\Column(name="date_create", type="datetime")
     */
    protected $dateCreate;
    /**
     * @var \DateTime
     * @ORM\Column(name="date_update", type="datetime")
     */
    protected $dateUpdate;
    /**
     * @ORM\PrePersist()
     */
    public function prePersist() {
        $this->dateCreate = new \DateTime();
        $this->dateUpdate = new \DateTime();
    }
    /**
     * @ORM\PreUpdate()
     */
    public function preUpdate(PreUpdateEventArgs $args) {
        $this->dateUpdate = new \DateTime();
    }
    /**
     * @return mixed
     */
    public function getDateCreate()
    {
        return $this->dateCreate->format('d.m.Y H:i:s');
    }
    /**
     * @param mixed $dateCreate
     * @return DateUpdateTrait
     */
    public function setDateCreate($dateCreate)
    {
        $this->dateCreate = $dateCreate;
        return $this;
    }
    /**
     * @return mixed
     */
    public function getDateUpdate()
    {
        return $this->dateUpdate->format('d.m.Y H:i:s');
    }
    /**
     * @param mixed $dateUpdate
     * @return DateUpdateTrait
     */
    public function setDateUpdate($dateUpdate)
    {
        $this->dateUpdate = $dateUpdate;
        return $this;
    }
}