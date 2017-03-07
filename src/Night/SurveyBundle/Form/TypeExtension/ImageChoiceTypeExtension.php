<?php
/**
 * Created by PhpStorm.
 * User: nightnr
 * Date: 17.01.17
 * Time: 19:39
 */

namespace Night\SurveyBundle\Form\TypeExtension;


use Night\SurveyBundle\Entity\Image;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\PropertyAccess\PropertyAccess;

class ImageChoiceTypeExtension extends AbstractTypeExtension
{

    /**
     * Returns the name of the type being extended.
     *
     * @return string The name of the type being extended
     */
    public function getExtendedType()
    {
        return ChoiceType::class;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefined(['image']);
        $resolver->setAllowedTypes('image', array('null', Image::class));
    }

    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        if(isset($options['image'])) {
            // set an "image_url" variable that will be available when rendering this field
            $view->vars['image'] = $options['image'];
        }
    }
}