<?php
/**
 * Created by PhpStorm.
 * User: nightnr
 * Date: 16.01.17
 * Time: 22:20
 */

namespace Night\SurveyBundle\TwigExtension;

class CustomRadioExtension extends \Twig_Extension
{
    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('row_radio', null)
        );
    }
}