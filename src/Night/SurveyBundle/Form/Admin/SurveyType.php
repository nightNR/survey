<?php
/**
 * Created by PhpStorm.
 * User: nightnr
 * Date: 03.07.17
 * Time: 21:00
 */

namespace Night\SurveyBundle\Form\Admin;


use function Sodium\add;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SurveyType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', TextType::class, array('label' => false, 'translation_domain' => 'NightSurveyBundle', 'attr' => ['placeholder' => 'survey.title']))
            ->add('save', SubmitType::class, ['label' => 'Save']);
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Night\SurveyBundle\Entity\Survey'
        ));
    }
}