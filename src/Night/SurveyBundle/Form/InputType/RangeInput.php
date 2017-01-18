<?php
/**
 * Created by PhpStorm.
 * User: nightnr
 * Date: 16.01.17
 * Time: 17:57
 */

namespace Night\SurveyBundle\Form\InputType;


use Night\SurveyBundle\Form\Transformer\RangeInputChoiceTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\ChoiceList\ArrayKeyChoiceList;
use Symfony\Component\Form\ChoiceList\Factory\CachingFactoryDecorator;
use Symfony\Component\Form\ChoiceList\Factory\PropertyAccessDecorator;
use Symfony\Component\Form\ChoiceList\LegacyChoiceListAdapter;
use Symfony\Component\Form\ChoiceList\View\ChoiceGroupView;
use Symfony\Component\Form\ChoiceList\ChoiceListInterface;
use Symfony\Component\Form\ChoiceList\Factory\DefaultChoiceListFactory;
use Symfony\Component\Form\ChoiceList\Factory\ChoiceListFactoryInterface;
use Symfony\Component\Form\ChoiceList\View\ChoiceListView;
use Symfony\Component\Form\ChoiceList\View\ChoiceView;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Symfony\Component\Form\Extension\Core\DataMapper\RadioListMapper;
use Symfony\Component\Form\Extension\Core\Type\RadioType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\Extension\Core\ChoiceList\ChoiceListInterface as LegacyChoiceListInterface;
use Symfony\Component\Form\Extension\Core\DataTransformer\ChoiceToValueTransformer;
use Symfony\Component\Form\Util\FormUtil;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RangeInput extends AbstractType
{
    /**
     * @internal To be removed in 3.0
     */
    const DEPRECATED_EMPTY_VALUE = '__deprecated_empty_value__';

    /**
     * Caches created choice lists.
     *
     * @var ChoiceListFactoryInterface
     */
    private $choiceListFactory;

    public function __construct(ChoiceListFactoryInterface $choiceListFactory = null)
    {
        $this->choiceListFactory = $choiceListFactory ?: new CachingFactoryDecorator(
            new PropertyAccessDecorator(
                new DefaultChoiceListFactory()
            )
        );
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->setDataMapper(new RadioListMapper());

        // Initialize all choices before doing the index check below.
        // This helps in cases where index checks are optimized for non
        // initialized choice lists. For example, when using an SQL driver,
        // the index check would read in one SQL query and the initialization
        // requires another SQL query. When the initialization is done first,
        // one SQL query is sufficient.

        $choiceListView = $this->createChoiceListView($options['choice_list'], $options);
        $builder->setAttribute('choice_list_view', $choiceListView);

        // Check if the choices already contain the empty value
        // Only add the placeholder option if this is not the case
        if (null !== $options['placeholder'] && 0 === count($options['choice_list']->getChoicesForValues(array('')))) {
            $placeholderView = new ChoiceView(null, '', $options['placeholder']);

            // "placeholder" is a reserved name
            $this->addSubForm($builder, 'placeholder', $placeholderView, $options);
        }

        $this->addSubForms($builder, $choiceListView->choices, $options);

        // Make sure that scalar, submitted values are converted to arrays
        // which can be submitted to the checkboxes/radio buttons
        $builder->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $event) {
            $form = $event->getForm();
            $data = $event->getData();

            if (null === $data) {
                $emptyData = $form->getConfig()->getEmptyData();

                if (false === FormUtil::isEmpty($emptyData) && array() !== $emptyData) {
                    $data = is_callable($emptyData) ? call_user_func($emptyData, $form, $data) : $emptyData;
                }
            }

            // Convert the submitted data to a string, if scalar, before
            // casting it to an array
            if (!is_array($data)) {
                $data = (array)(string)$data;
            }

            // A map from submitted values to integers
            $valueMap = array_flip($data);

            // Make a copy of the value map to determine whether any unknown
            // values were submitted
            $unknownValues = $valueMap;

            // Reconstruct the data as mapping from child names to values
            $data = array();

            foreach ($form as $child) {
                $value = $child->getConfig()->getOption('value');

                // Add the value to $data with the child's name as key
                if (isset($valueMap[$value])) {
                    $data[$child->getName()] = $value;
                    unset($unknownValues[$value]);
                    continue;
                }
            }

            // The empty value is always known, independent of whether a
            // field exists for it or not
            unset($unknownValues['']);

            // Throw exception if unknown values were submitted
            if (count($unknownValues) > 0) {
                throw new TransformationFailedException(sprintf(
                    'The choices "%s" do not exist in the choice list.',
                    implode('", "', array_keys($unknownValues))
                ));
            }

            $event->setData($data);
        });

        $builder->addViewTransformer(new RangeInputChoiceTransformer($options['choice_list']));
    }

    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $choiceTranslationDomain = $options['choice_translation_domain'];
        if ($view->parent && null === $choiceTranslationDomain) {
            $choiceTranslationDomain = $view->vars['translation_domain'];
        }

        /** @var ChoiceListView $choiceListView */
        $choiceListView = $form->getConfig()->hasAttribute('choice_list_view')
            ? $form->getConfig()->getAttribute('choice_list_view')
            : $this->createChoiceListView($options['choice_list'], $options);

        $view->vars = array_replace($view->vars, array(
            'preferred_choices' => $choiceListView->preferredChoices,
            'choices' => $choiceListView->choices,
            'separator' => '-------------------',
            'placeholder' => null,
            'choice_translation_domain' => $choiceTranslationDomain,
        ));


        $view->vars['is_selected'] = function ($choice, $value) {
            return $choice === $value;
        };

        // Check if the choices already contain the empty value
        $view->vars['placeholder_in_choices'] = $choiceListView->hasPlaceholder();

        // Only add the empty value option if this is not the case
        if (null !== $options['placeholder'] && !$view->vars['placeholder_in_choices']) {
            $view->vars['placeholder'] = $options['placeholder'];
        }

        // BC
        $view->vars['empty_value'] = $view->vars['placeholder'];
        $view->vars['empty_value_in_choices'] = $view->vars['placeholder_in_choices'];
        $view->vars['min_label'] = $form->getConfig()->hasOption('min_label')
            ? $form->getConfig()->getOption('min_label')
            : '';
        $view->vars['max_label'] = $form->getConfig()->hasOption('max_label')
            ? $form->getConfig()->getOption('max_label')
            : '';
    }

    /**
     * {@inheritdoc}
     */
    public function finishView(FormView $view, FormInterface $form, array $options)
    {
        // Radio buttons should have the same name as the parent
        $childName = $view->vars['full_name'];

        foreach ($view as $childView) {
            $childView->vars['full_name'] = $childName;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $choiceLabels = (object)array('labels' => array());
        $choiceListFactory = $this->choiceListFactory;

        $emptyData = function (Options $options) {
            return;
        };

        $placeholder = function (Options $options) {
            return $options['required'] ? null : '';
        };

        // BC closure, to be removed in 3.0
        $choicesNormalizer = function (Options $options, $choices) use ($choiceLabels) {
            // Unset labels from previous invocations
            $choiceLabels->labels = array();

            // This closure is irrelevant when "choices_as_values" is set to true
            if ($options['choices_as_values']) {
                return $choices;
            }

            if (null === $choices) {
                return;
            }

            return self::normalizeLegacyChoices($choices, $choiceLabels);
        };

        // BC closure, to be removed in 3.0
        $choiceLabel = function (Options $options) use ($choiceLabels) {
            // If the choices contain duplicate labels, the normalizer of the
            // "choices" option stores them in the $choiceLabels variable

            // Trigger the normalizer
            $options->offsetGet('choices');

            // Pick labels from $choiceLabels if available
            if ($choiceLabels->labels) {
                // Don't pass the labels by reference. We do want to create a
                // copy here so that every form has an own version of that
                // variable (contrary to the $choiceLabels object shared by all
                // forms)
                $labels = $choiceLabels->labels;

                // The $choiceLabels object is shared with the 'choices' closure.
                // Since that normalizer can be replaced, labels have to be cleared here.
                $choiceLabels->labels = array();

                return function ($choice, $key) use ($labels) {
                    return $labels[$key];
                };
            }

            return;
        };

        $that = $this;
        $choiceListNormalizer = function (Options $options, $choiceList) use ($choiceListFactory, $that) {
            if ($choiceList) {
                @trigger_error(sprintf('The "choice_list" option of the "%s" form type (%s) is deprecated since version 2.7 and will be removed in 3.0. Use "choice_loader" instead.', $that->getName(), __CLASS__), E_USER_DEPRECATED);

                if ($choiceList instanceof LegacyChoiceListInterface) {
                    return new LegacyChoiceListAdapter($choiceList);
                }

                return $choiceList;
            }

            if (null !== $options['choice_loader']) {
                return $choiceListFactory->createListFromLoader(
                    $options['choice_loader'],
                    $options['choice_value']
                );
            }

            // Harden against NULL values (like in EntityType and ModelType)
            $choices = null !== $options['choices'] ? $options['choices'] : array();

            // BC when choices are in the keys, not in the values
            if (!$options['choices_as_values']) {
                return $choiceListFactory->createListFromFlippedChoices($choices, $options['choice_value'], false);
            }

            return $choiceListFactory->createListFromChoices($choices, $options['choice_value']);
        };

        $choicesAsValuesNormalizer = function (Options $options, $choicesAsValues) use ($that) {
            if (true !== $choicesAsValues) {
                @trigger_error(sprintf('The value "false" for the "choices_as_values" option of the "%s" form type (%s) is deprecated since version 2.8 and will not be supported anymore in 3.0. Set this option to "true" and flip the contents of the "choices" option instead.', $that->getName(), __CLASS__), E_USER_DEPRECATED);
            }

            return $choicesAsValues;
        };

        $placeholderNormalizer = function (Options $options, $placeholder) use ($that) {
            if ($that::DEPRECATED_EMPTY_VALUE !== $options['empty_value']) {
                @trigger_error(sprintf('The form option "empty_value" of the "%s" form type (%s) is deprecated since version 2.6 and will be removed in 3.0. Use "placeholder" instead.', $that->getName(), __CLASS__), E_USER_DEPRECATED);

                if (null === $placeholder || '' === $placeholder) {
                    $placeholder = $options['empty_value'];
                }
            }

            if ($options['required'] && ($options['expanded'] || isset($options['attr']['size']) && $options['attr']['size'] > 1)) {
                // placeholder for required radio buttons or a select with size > 1 does not make sense
                return;
            } elseif (false === $placeholder) {
                // an empty value should be added but the user decided otherwise
                return;
            } elseif ($options['expanded'] && '' === $placeholder) {
                // never use an empty label for radio buttons
                return 'None';
            }

            // empty value has been set explicitly
            return $placeholder;
        };

        $minLabelNormalizer = function (Options $options, $data) use ($that) {
            $choices = $options['choices'];
            $label = "";
            $minKey = null;
            foreach($choices as $key => $choice) {
                if($minKey === null || $minKey > $key) {
                    $minKey = $key;
                    $label = $options['choice_label']($key, $choice);
                }
            }
            return $label;
        };

        $maxLabelNormalizer = function (Options $options, $data) use ($that) {
            $choices = $options['choices'];
            $label = "";
            $maxKey = null;
            foreach($choices as $key => $choice) {
                if($maxKey === null || $maxKey < $key) {
                    $label = $options['choice_label']($key, $choice);
                }
            }
            return $label;
        };

        $compound = function (Options $options) {
            return $options['expanded'];
        };

        $choiceTranslationDomainNormalizer = function (Options $options, $choiceTranslationDomain) {
            if (true === $choiceTranslationDomain) {
                return $options['translation_domain'];
            }

            return $choiceTranslationDomain;
        };

        $resolver->setDefaults(array(
            'expanded' => true,
            'choices' => array(),
            'choice_list' => null, // deprecated
            'choices_as_values' => false,
            'choice_loader' => null,
            'choice_label' => $choiceLabel,
            'choice_name' => null,
            'choice_value' => null,
            'choice_attr' => null,
            'preferred_choices' => array(),
            'group_by' => null,
            'empty_data' => $emptyData,
            'empty_value' => self::DEPRECATED_EMPTY_VALUE,
            'placeholder' => $placeholder,
            'error_bubbling' => false,
            'compound' => $compound,
            // The view data is always a string, even if the "data" option
            // is manually set to an object.
            // See https://github.com/symfony/symfony/pull/5582
            'data_class' => null,
            'choice_translation_domain' => true,
            'min_label' => '',
            'max_label' => '',
        ));

        $resolver->setNormalizer('min_label', $minLabelNormalizer);
        $resolver->setNormalizer('max_label', $maxLabelNormalizer);
        $resolver->setNormalizer('choices', $choicesNormalizer);
        $resolver->setNormalizer('choice_list', $choiceListNormalizer);
        $resolver->setNormalizer('placeholder', $placeholderNormalizer);
        $resolver->setNormalizer('choice_translation_domain', $choiceTranslationDomainNormalizer);
        $resolver->setNormalizer('choices_as_values', $choicesAsValuesNormalizer);

        $resolver->setAllowedTypes('choices', array('null', 'array', '\Traversable'));
        $resolver->setAllowedTypes('choice_translation_domain', array('null', 'bool', 'string'));
        $resolver->setAllowedTypes('choices_as_values', 'bool');
        $resolver->setAllowedTypes('choice_loader', array('null', 'Symfony\Component\Form\ChoiceList\Loader\ChoiceLoaderInterface'));
        $resolver->setAllowedTypes('choice_label', array('null', 'bool', 'callable', 'string', 'Symfony\Component\PropertyAccess\PropertyPath'));
        $resolver->setAllowedTypes('choice_name', array('null', 'callable', 'string', 'Symfony\Component\PropertyAccess\PropertyPath'));
        $resolver->setAllowedTypes('choice_value', array('null', 'callable', 'string', 'Symfony\Component\PropertyAccess\PropertyPath'));
        $resolver->setAllowedTypes('choice_attr', array('null', 'array', 'callable', 'string', 'Symfony\Component\PropertyAccess\PropertyPath'));
        $resolver->setAllowedTypes('preferred_choices', array('array', '\Traversable', 'callable', 'string', 'Symfony\Component\PropertyAccess\PropertyPath'));
        $resolver->setAllowedTypes('group_by', array('null', 'callable', 'string', 'Symfony\Component\PropertyAccess\PropertyPath'));
        $resolver->setAllowedTypes('min_label', array('string'));
        $resolver->setAllowedTypes('max_label', array('string'));
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->getBlockPrefix();
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'radio_range';
    }

    /**
     * Adds the sub fields for an expanded choice field.
     *
     * @param FormBuilderInterface $builder The form builder
     * @param array $choiceViews The choice view objects
     * @param array $options The build options
     */
    private function addSubForms(FormBuilderInterface $builder, array $choiceViews, array $options)
    {
            // Flatten groups
            if (is_array($choiceViews)) {
                if(count($choiceViews) != 2) {
                    throw new \InvalidArgumentException(sprintf("Wrong count of choices provided. Required two %s provided instead.", count($choiceViews)));
                }
                usort($choiceViews, function(ChoiceView $a, ChoiceView $b){
                    return $a->value - $b->value;
                });
                $min = array_shift($choiceViews);
                $max = array_shift($choiceViews);

                $generatorFunction = function(ChoiceView $a, ChoiceView $b) {
                    $pivot = ($b->value - $a->value) / 2;
                    for($i = $a->value; $i <= $b->value; $i++) {
                        $label = $i < $pivot ? $a->label : $b->label;
                        yield new ChoiceView($i, (string) $i, $label, $a->attr);
                    }
                };

                foreach($generatorFunction($min, $max) as $key => $choiceView) {
                    $this->addSubForm($builder, $key, $choiceView, $options);
                }
            }
    }

    /**
     * @param FormBuilderInterface $builder
     * @param                      $name
     * @param                      $choiceView
     * @param array $options
     *
     * @return mixed
     */
    private function addSubForm(FormBuilderInterface $builder, $name, ChoiceView $choiceView, array $options)
    {
        $choiceOpts = array(
            'value' => $choiceView->value,
            'label' => $choiceView->label,
            'attr' => $choiceView->attr,
            'translation_domain' => $options['translation_domain'],
            'block_name' => 'entry',
        );

        $builder->add($name, RadioType::class, $choiceOpts);
    }

    private function createChoiceListView(ChoiceListInterface $choiceList, array $options)
    {
        return $this->choiceListFactory->createView(
            $choiceList,
            $options['preferred_choices'],
            $options['choice_label'],
            $options['choice_name'],
            $options['group_by'],
            $options['choice_attr']
        );
    }

    /**
     * When "choices_as_values" is set to false, the choices are in the keys and
     * their labels in the values. Labels may occur twice. The form component
     * flips the choices array in the new implementation, so duplicate labels
     * are lost. Store them in a utility array that is used from the
     * "choice_label" closure by default.
     *
     * @param array|\Traversable $choices The choice labels indexed by choices
     * @param object $choiceLabels The object that receives the choice labels
     *                                         indexed by generated keys.
     * @param int $nextKey The next generated key
     *
     * @return array The choices in a normalized array with labels replaced by generated keys
     *
     * @internal Public only to be accessible from closures on PHP 5.3. Don't
     *           use this method as it may be removed without notice and will be in 3.0.
     */
    public static function normalizeLegacyChoices($choices, $choiceLabels, &$nextKey = 0)
    {
        $normalizedChoices = array();

        foreach ($choices as $choice => $choiceLabel) {
            if (is_array($choiceLabel) || $choiceLabel instanceof \Traversable) {
                $normalizedChoices[$choice] = self::normalizeLegacyChoices($choiceLabel, $choiceLabels, $nextKey);
                continue;
            }

            $choiceLabels->labels[$nextKey] = $choiceLabel;
            $normalizedChoices[$choice] = $nextKey++;
        }

        return $normalizedChoices;
    }
}