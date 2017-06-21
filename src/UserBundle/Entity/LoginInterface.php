<?php
/**
 * Created by PhpStorm.
 * User: pbalaz
 * Date: 10/27/16
 * Time: 12:20 PM
 */

namespace UserBundle\Entity;


interface LoginInterface
{
    public function getUser();
    public function setUser(User $user);
    public function getId();
    public function setId($id);
    public function getAccessToken();
    public function setAccessToken($accessToken);
}