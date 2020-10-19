<?php

/*
 * @copyright   2016 Mautic Contributors. All rights reserved
 * @author      Mautic
 *
 * @link        http://mautic.org
 *
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace MauticPlugin\MauticRecommenderBundle\Form\Type;

use JMS\Serializer\Tests\Fixtures\Input;
use Mautic\CoreBundle\Helper\InputHelper;
use MauticPlugin\MauticRecommenderBundle\Api\Service\ApiCommands;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class RecommenderTagsType.
 */
class RecommenderTagsType extends AbstractType
{
    /**
     * @var ApiCommands
     */
    private $apiCommands;

    /**
     * RecommenderTagsType constructor.
     */
    public function __construct(ApiCommands $apiCommands)
    {
        $this->apiCommands = $apiCommands;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'choices' => function (Options $options) {
                $properties =  $this->apiCommands->callCommand('ListProperties');
                $choices = [];
                foreach ($properties as $property) {
                    $tag = '{{ '.InputHelper::alphanum(InputHelper::transliterate($property['name'])).' }}';
                    $choices[$tag] = $property['name'];
                }

                return array_flip($choices);
            },
            'label'       => 'mautic.plugin.recommender.template.tags',
            'label_attr'  => ['class' => 'control-label'],
            'multiple'    => false,
            'required'    => false,
        ]);
    }

    /**
     * @return string
     */
    public function getBlockPrefix()
    {
        return 'recommender_tags';
    }

    /**
     * @return string
     */
    public function getParent()
    {
        return ChoiceType::class;
    }
}
