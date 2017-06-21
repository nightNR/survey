<?php
/**
 * Created by PhpStorm.
 * User: pbalaz
 * Date: 10/27/16
 * Time: 5:09 PM
 */

namespace UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class Setting
 * @package UserBundle\Entity
 * @ORM\Entity()
 * @ORM\Table(name="user_setting")
 */
class Setting
{

    const MENU_TOGGLE = 1;

    public static $DEFAULT_VALUES = [
        self::MENU_TOGGLE => false
    ];

    /**
     * @var
     * @ORM\ManyToOne(targetEntity="UserBundle\Entity\User", cascade={"persist"}, inversedBy="settings")
     * @ORM\Id()
     */
    protected $user;

    /**
     * @var
     * @ORM\Column(name="setting_id", type="integer", nullable=false)
     * @ORM\Id()
     */
    protected $settingId;

    /**
     * @var
     * @ORM\Column(name="value", type="string", nullable=false)
     */
    protected $value;

    /**
     * @return mixed
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param mixed $user
     */
    public function setUser($user)
    {
        $this->user = $user;
    }

    /**
     * @return mixed
     */
    public function getSettingId()
    {
        return $this->settingId;
    }

    /**
     * @param mixed $settingId
     */
    public function setSettingId($settingId)
    {
        $this->settingId = $settingId;
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @param mixed $value
     */
    public function setValue($value)
    {
        $this->value = $value;
    }


}