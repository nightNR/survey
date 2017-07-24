<?php
/**
 * Created by PhpStorm.
 * User: pbalaz
 * Date: 7/4/17
 * Time: 5:20 PM
 */

namespace Night\SurveyBundle\Form\Admin;

use Night\SurveyBundle\Form\Admin\Transformer\SurveyTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Night\SurveyBundle\Entity\Form;

class FormType extends AbstractType
{
    /**
     * @var SurveyTransformer
     */
    private $surveyTransformer;

    /**
     * FormType constructor.
     *
     * @param SurveyTransformer $surveyTransformer
     */
    public function __construct(SurveyTransformer $surveyTransformer)
    {
        $this->surveyTransformer = $surveyTransformer;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('survey', HiddenType::class)
            ->add('top_text', TextType::class, array('label' => false, 'translation_domain' => 'NightSurveyBundle', 'attr' => ['placeholder' => 'form.title']))
            ->add('topic', TextType::class, array('label' => false, 'translation_domain' => 'NightSurveyBundle', 'attr' => ['placeholder' => 'form.topic']))
            ->add('is_scs', CheckboxType::class, array('label' => 'form.is_scs', 'translation_domain' => 'NightSurveyBundle', 'required' => false))
            ->add('save', SubmitType::class, ['label' => 'Save']);

        $builder->get('survey')->addModelTransformer($this->surveyTransformer);
    }

    /**
     * @param OptionsResolver $resolver
     *
     * @throws \Symfony\Component\OptionsResolver\Exception\AccessException
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => Form::class
        ));
    }
}
