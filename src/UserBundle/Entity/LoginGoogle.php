<?php
/**
 * Created by PhpStorm.
 * User: pbalaz
 * Date: 10/25/16
 * Time: 4:24 PM
 */

namespace UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class LoginGoogle
 * @package UserBundle\Entity
 * @ORM\Entity()
 * @ORM\Table(name="login_google")
 */
class LoginGoogle implements LoginInterface
{
    /**
     * @var
     * @ORM\OneToOne(targetEntity="UserBundle\Entity\User", inversedBy="googleLogin")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    protected $user;

    /**
     * @var
     * @ORM\Id()
     * @ORM\Column(name="id", type="string", length=255, nullable=false)
     */
    protected $id;

    /**
     * @var
     * @ORM\Column(name="access_token", type="string", length=255, nullable=true)
     */
    protected $accessToken;

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
    public function setUser(User $user)
    {
        $this->user = $user;
        if($user->getGoogleLogin() === null) {
            $user->setGoogleLogin($this);
        }
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getAccessToken()
    {
        return $this->accessToken;
    }

    /**
     * @param mixed $accessToken
     */
    public function setAccessToken($accessToken)
    {
        $this->accessToken = $accessToken;
    }


}