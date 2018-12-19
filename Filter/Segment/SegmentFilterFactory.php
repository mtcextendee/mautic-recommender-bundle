<?php

/*
 * @copyright   2018 Mautic Contributors. All rights reserved
 * @author      Mautic
 *
 * @link        http://mautic.org
 *
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace MauticPlugin\MauticRecommenderBundle\Filter\Segment;


use Mautic\LeadBundle\Segment\ContactSegmentFilter;
use Mautic\LeadBundle\Segment\ContactSegmentFilterCrate;
use Mautic\LeadBundle\Segment\Query\QueryBuilder;
use Mautic\LeadBundle\Segment\TableSchemaColumnsCache;
use MauticPlugin\MauticRecommenderBundle\Filter\Segment\Decorator\Decorator;
use MauticPlugin\MauticRecommenderBundle\Filter\Segment\EventListener\Choices;
use Symfony\Component\DependencyInjection\ContainerInterface;

class SegmentFilterFactory
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @var TableSchemaColumnsCache
     */
    private $schemaCache;

    /**
     * @var Decorator
     */
    private $decorator;

    /**
     * @var Choices
     */
    private $segmentChoices;

    /**
     * SegmentFilterFactory constructor.
     *
     * @param ContainerInterface      $container
     * @param TableSchemaColumnsCache $schemaCache
     * @param Decorator               $decorator
     * @param Choices                 $segmentChoices
     */
    public function __construct(ContainerInterface $container, TableSchemaColumnsCache $schemaCache, Decorator $decorator, Choices $segmentChoices)
    {

        $this->container = $container;
        $this->schemaCache = $schemaCache;
        $this->decorator = $decorator;
        $this->segmentChoices = $segmentChoices;
    }

    public function applySegmentQuery($filter, QueryBuilder $qb)
    {
        $contactSegmentFilterCrate = new ContactSegmentFilterCrate($filter);
        $filterQueryBuilder = $this->container->get($this->decorator->getQueryType($contactSegmentFilterCrate));
        $contactSegmentFilter = new ContactSegmentFilter($contactSegmentFilterCrate, $this->decorator, $this->schemaCache, $filterQueryBuilder);
        $filterQueryBuilder->applyQuery($qb, $contactSegmentFilter);

    }
}
