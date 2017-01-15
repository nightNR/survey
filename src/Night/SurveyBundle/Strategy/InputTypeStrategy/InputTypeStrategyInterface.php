<?php
/**
 * Created by PhpStorm.
 * User: nightnr
 * Date: 15.01.17
 * Time: 21:14
 */

namespace Night\SurveyBundle\Strategy\InputTypeStrategy;


use Night\SurveyBundle\Entity\Question;

interface InputTypeStrategyInterface
{
    public function getInputType();

    public function process(Question $question);
}