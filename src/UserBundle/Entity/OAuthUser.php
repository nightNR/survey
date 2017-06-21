<?php
/**
 * Created by PhpStorm.
 * User: pbalaz
 * Date: 10/25/16
 * Time: 3:11 PM
 */

namespace UserBundle\Entity;


use HWI\Bundle\OAuthBundle\OAuth\Response\UserResponseInterface;

class OAuthUser
{
    protected $firstName;

    protected $lastName;

    protected $name;

    protected $email;

    protected $googleId;

    protected $googleAccessToken;

    protected $facebookId;

    protected $facebookAccessToken;

    public function __construct(UserResponseInterface $response)
    {
        $this->firstName = $response->getFirstName();
        $this->lastName = $response->getLastName();
        $this->name = $response->getRealName();
        $this->email = $response->getEmail();

        $resourceOwnerName = $response->getResourceOwner()->getName();

        $idPropertyName = $resourceOwnerName."Id";
        $accessTokenPropertyName = $resourceOwnerName."AccessToken";

        $this->$idPropertyName = $response->getUsername();
        $this->$accessTokenPropertyName = $response->getAccessToken();
    }

    /**
     * @return null|string
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * @return null|string
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * @return null|string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return null|string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getAccessToken()
    {
        return $this->accessToken;
    }

    public function asArray()
    {
        return [
            'first_name' => $this->firstName,
            'last_name' => $this->lastName,
            'name' => $this->name,
            'email' => $this->email,
            'google_id' => $this->googleId,
            'google_access_token' => $this->googleAccessToken,
            'facebook_id' => $this->facebookId,
            'facebook_access_token' => $this->facebookAccessToken,
        ];
    }


}