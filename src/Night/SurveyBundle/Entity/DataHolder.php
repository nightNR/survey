<?php
/**
 * Created by PhpStorm.
 * User: nightnr
 * Date: 15.01.17
 * Time: 20:09
 */

namespace Night\SurveyBundle\Entity;


class DataHolder
{
    protected $data = [];

    function __get($name)
    {
        return isset($this->data[$name])?$this->data[$name]:null;
    }

    function __isset($name)
    {
        return isset($this->data[$name]);
    }

    function __set($name, $value)
    {
        $this->data[$name] = $value;
    }
}