<?php
/**
 * Created by PhpStorm.
 * User: pbalaz
 * Date: 10/27/16
 * Time: 6:00 PM
 */

namespace Night\SurveyBundle\Service\ApiCommands;


class Setting extends AbstractApiService
{

    public function getName()
    {
        return "setting";
    }

    public function changeValue($id, $value = null)
    {
        $user = $this->getUser();
        if($user === null){
            return;
        }
        $setting = $user->getSetting($id);
        if($value == null){
            $value = $setting->getValue();
            $setting->setValue(!$value);
        } else {
            $setting->setValue($value);
        }
        $this->em->persist($setting);
        $this->em->flush();
    }
}