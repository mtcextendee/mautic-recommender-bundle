<?php

/*
 * @copyright   2016 Mautic Contributors. All rights reserved
 * @author      Mautic
 *
 * @link        http://mautic.org
 *
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace MauticPlugin\MauticRecommenderBundle\Filter\EventListener;

use Mautic\CoreBundle\CoreEvents;
use Mautic\CoreBundle\Event\BuildJsEvent;
use Mautic\CoreBundle\EventListener\CommonSubscriber;
use Mautic\CoreBundle\Helper\CoreParametersHelper;
use Mautic\LeadBundle\Event\LeadListFilteringEvent;
use Mautic\LeadBundle\Event\LeadListFiltersChoicesEvent;
use Mautic\LeadBundle\Event\LeadListQueryBuilderGeneratedEvent;
use Mautic\LeadBundle\LeadEvents;
use Mautic\LeadBundle\Model\ListModel;
use Mautic\LeadBundle\Segment\ContactSegmentFilter;
use Mautic\LeadBundle\Segment\ContactSegmentFilterCrate;
use Mautic\LeadBundle\Segment\Decorator\DecoratorFactory;
use Mautic\LeadBundle\Segment\Decorator\FilterDecoratorInterface;
use Mautic\LeadBundle\Segment\Query\Filter\EventFilterQueryBuilder;
use Mautic\LeadBundle\Segment\Query\Filter\ForeignValueFilterQueryBuilder;
use Mautic\LeadBundle\Segment\TableSchemaColumnsCache;
use MauticPlugin\MauticRecommenderBundle\Filter\EventDecorator;
use MauticPlugin\MauticRecommenderBundle\Filter\FilterFields\SegmentChoices;
use MauticPlugin\MauticRecommenderBundle\Helper\RecommenderHelper;
use MauticPlugin\MauticRecommenderBundle\Model\RecommenderClientModel;
use Symfony\Component\DependencyInjection\ContainerInterface;

class SegmentFiltersSubscriber extends CommonSubscriber
{
    /**
     * @var ListModel
     */
    private $listModel;

    /**
     * @var RecommenderClientModel
     */
    private $recommenderClientModel;

    /**
     * @var DecoratorFactory
     */
    private $decoratorFactory;

    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @var TableSchemaColumnsCache
     */
    private $schemaCache;

    /**
     * @var EventDecorator
     */
    private $decorator;

    /**
     * @var SegmentChoices
     */
    private $segmentChoices;


    /**
     * SegmentFiltersSubscriber constructor.
     *
     * @param ListModel               $listModel
     * @param RecommenderClientModel  $recommenderClientModel
     * @param DecoratorFactory        $decoratorFactory
     * @param ContainerInterface      $container
     * @param TableSchemaColumnsCache $schemaCache
     * @param EventDecorator          $decorator
     * @param SegmentChoices          $segmentChoices
     */
    public function __construct(ListModel $listModel, RecommenderClientModel $recommenderClientModel, DecoratorFactory $decoratorFactory, ContainerInterface $container, TableSchemaColumnsCache $schemaCache, EventDecorator $decorator, SegmentChoices $segmentChoices)
    {

        $this->listModel = $listModel;
        $this->recommenderClientModel = $recommenderClientModel;
        $this->decoratorFactory = $decoratorFactory;
        $this->container = $container;
        $this->schemaCache = $schemaCache;
        $this->decorator = $decorator;
        $this->segmentChoices = $segmentChoices;
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            LeadEvents::LIST_FILTERS_CHOICES_ON_GENERATE => [
                ['onListFiltersGenerate', 0],
            ],

            LeadEvents::LIST_FILTERS_ON_FILTERING => [
                ['onListFiltersFiltering', 0],
            ],
        ];
    }

    public function onListFiltersFiltering(LeadListFilteringEvent $event)
    {
        $qb =$event->getQueryBuilder();
        $details = $event->getDetails();
        $contactSegmentFilterCrate = new ContactSegmentFilterCrate($details);
      //  $decorator = $this->decoratorFactory->getDecoratorForFilter($contactSegmentFilterCrate);
        $decorator = $this->decorator;
        $dictionary['event'] = [
            'type'          => ForeignValueFilterQueryBuilder::getServiceId(),
            'foreign_table' => 'recommender_event_log',
            'field'         => 'event_id',
        ];
        $decorator->setDictionary($dictionary);

        /** @var ForeignValueFilterQueryBuilder $filterQueryBuilder */
        $filterQueryBuilder = $this->getQueryBuilderForFilter($decorator, $contactSegmentFilterCrate);

        $contactSegmentFilter = new ContactSegmentFilter($contactSegmentFilterCrate, $decorator, $this->schemaCache, $filterQueryBuilder);

        $qb = $event->getQueryBuilder();
        $filterQueryBuilder->applyQuery($qb, $contactSegmentFilter);
        $event->setFilteringStatus(true);
    }

    /**
     * @param FilterDecoratorInterface  $decorator
     * @param ContactSegmentFilterCrate $contactSegmentFilterCrate
     *
     * @return FilterQueryBuilderInterface
     *
     * @throws \Exception
     */
    private function getQueryBuilderForFilter(FilterDecoratorInterface $decorator, ContactSegmentFilterCrate $contactSegmentFilterCrate)
    {
        $qbServiceId = $decorator->getQueryType($contactSegmentFilterCrate);
        return $this->container->get($qbServiceId);
    }

    /**
     * @param BuildJsEvent $event
     */
    public function onListFiltersGenerate(LeadListFiltersChoicesEvent $event)
    {
        $this->segmentChoices->addChoices($event);
    }
}
