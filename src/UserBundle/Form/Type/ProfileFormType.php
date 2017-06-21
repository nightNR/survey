<?php

/*
 * This file is part of the FOSUserBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace UserBundle\Form\Type;

use FOS\UserBundle\Util\LegacyFormHelper;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use FOS\UserBundle\Form\Type\ProfileFormType as FOSProfileFormType;
use Symfony\Component\Form\Extension\Core\Type\CountryType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Security\Core\Validator\Constraints\UserPassword;

class ProfileFormType extends FOSProfileFormType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $this->buildUserForm($builder, $options);

        $builder->add('current_password', LegacyFormHelper::getType(
            'Symfony\Component\Form\Extension\Core\Type\PasswordType'
        ), array(
            'label' => false,
            'translation_domain' => 'FOSUserBundle',
            'mapped' => false,
            'constraints' => new UserPassword(),
            'attr' => ['placeholder' => 'form.current_password']
        ));
    }

    /**
     * Builds the embedded form representing the user.
     *
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    protected function buildUserForm(FormBuilderInterface $builder, array $options)
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
            ));
    }
}
