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

use MauticPlugin\MauticRecommenderBundle\Entity\Event;
use MauticPlugin\MauticRecommenderBundle\Model\RecommenderEventModel;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ListEventsType extends AbstractType
{
    /**
     * @var RecommenderEventModel
     */
    private $recommenderEventModel;

    /**
     * EventsListType constructor.
     */
    public function __construct(RecommenderEventModel $recommenderEventModel)
    {
        $this->recommenderEventModel = $recommenderEventModel;
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
                $events  = $this->recommenderEventModel->getRepository()->findAll();
                $choices = [];
                /** @var Event $event */
                foreach ($events as $event) {
                    $choices[$event->getId()] = $event->getName();
                }

                return $choices;
            },
            'attr'        => [
                'class' => 'form-control',
            ],
            'label'       => '',
            'expanded'    => false,
            'multiple'    => false,
            'required'    => false,
            'placeholder' => '',
        ]);
    }

    /**
     * @return string
     */
    public function getParent()
    {
        return ChoiceType::class;
    }
}
