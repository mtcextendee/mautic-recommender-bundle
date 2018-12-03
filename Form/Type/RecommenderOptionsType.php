<?php

/*
 * @copyright   2016 Mautic, Inc. All rights reserved
 * @author      Mautic, Inc
 *
 * @link        https://mautic.org
 *
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace MauticPlugin\MauticRecommenderBundle\Form\Type;

use MauticPlugin\MauticRecommenderBundle\Event\FilterFormEvent;
use MauticPlugin\MauticRecommenderBundle\RecommenderEvents;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Range;

/**
 * Class RecommenderOptionsType.
 */
class RecommenderOptionsType extends AbstractType
{
    /**
     * @var EventDispatcherInterface
     */
    private $dispatcher;

    /**
     * RecommenderOptionsType constructor.
     *
     * @param EventDispatcher $dispatcher
     */
    public function __construct(EventDispatcherInterface $dispatcher)
    {

        $this->dispatcher = $dispatcher;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        if ($this->dispatcher->hasListeners(RecommenderEvents::ON_RECOMMENDER_FILTER_FORM_DISPLAY)) {
            $event = new FilterFormEvent($builder);
            $this->dispatcher->dispatch(RecommenderEvents::ON_RECOMMENDER_FILTER_FORM_DISPLAY, $event);
            unset($event);
        }

    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'recommender_options_type';
    }
}
