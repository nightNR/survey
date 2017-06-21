<?php
/**
 * Created by PhpStorm.
 * User: pbalaz
 * Date: 10/27/16
 * Time: 10:52 AM
 */

namespace UserBundle\Form\Transformer;


use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Doctrine\Common\Persistence\ObjectManager;
use UserBundle\Entity\LoginGoogle;

class LoginGoogleTransformer implements DataTransformerInterface
{
    /**
     * @var ObjectManager
     */
    private $em;

    public function __construct(ObjectManager $em)
    {
        $this->em = $em;
    }

    /**
     * @inheritdoc
     */
    public function transform($value)
    {
        if($value === null) {
            return '';
        }
        return implode('|', [
            $value->getUser()->getId(),
            $value->getId(),
            $value->getAccessToken()
        ]);
    }

    /**
     * @inheritdoc
     */
    public function reverseTransform($value)
    {
        if(!$value) {
            return null;
        }

        $data = explode('|', $value);

        /** @var \UserBundle\Entity\User $user */
        $user = $this->em->getRepository('UserBundle:User')->find($data[0]);

        if($user !== null) {
            $loginGoogle = $user->getGoogleLogin();
            if($loginGoogle !== null) {
                $loginGoogle->setId($data[1]);
                $loginGoogle->setAccessToken($data[2]);
                return $loginGoogle;
            }
        }
        $loginGoogle = new LoginGoogle();
        $loginGoogle->setId($data[1]);
        $loginGoogle->setAccessToken($data[2]);
        return $loginGoogle;
    }
}