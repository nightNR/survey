<?php
namespace UserBundle\Form\Type;

use Doctrine\Common\Persistence\ObjectManager;
use EWZ\Bundle\RecaptchaBundle\Form\Type\EWZRecaptchaType;
use EWZ\Bundle\RecaptchaBundle\Validator\Constraints\IsTrue;
use FOS\UserBundle\Form\Type\RegistrationFormType as FOSRegistrationFormType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use UserBundle\Form\Transformer\LoginFacebookTransformer;
use UserBundle\Form\Transformer\LoginGoogleTransformer;

/**
 * Created by PhpStorm.
 * User: pbalaz
 * Date: 3/6/16
 * Time: 11:15 AM
 */
class RegistrationFormType extends AbstractType
{
    /**
     * @var ObjectManager
     */
    protected $em;

    private $class;

    public function __construct($class, ObjectManager $em)
    {
        $this->em = $em;
        $this->class = $class;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email', EmailType::class, array(
                'label' => false,
                'translation_domain' => 'FOSUserBundle',
                'attr' => [
                    'placeholder' => 'form.email'
                ]
            ))
            ->add('username', TextType::class, array(
                'label' => false,
                'translation_domain' => 'FOSUserBundle',
                'attr' => [
                    'placeholder' => 'form.username'
                ]
            ))
            ->add('plainPassword', RepeatedType::class, array(
                'type' => PasswordType::class,
                'options' => array('translation_domain' => 'FOSUserBundle'),
                'first_options' => array('label' => false, 'attr' => ['placeholder' => 'form.password']),
                'second_options' => array('label' => false, 'attr' => ['placeholder' => 'form.password_confirmation']),
                'invalid_message' => 'fos_user.password.mismatch',
            ))
            ->add('googleLogin', HiddenType::class)
            ->add('facebookLogin', HiddenType::class);
        $builder->get('googleLogin')->addModelTransformer(new LoginGoogleTransformer($this->em));
        $builder->get('facebookLogin')->addModelTransformer(new LoginFacebookTransformer($this->em));
    }

    public function getParent()
    {
        return "fos_user_registration";
    }

    public function getName()
    {
        return "app_user_registration";
    }
}
