<?php

/*
 * @copyright   2016 Mautic Contributors. All rights reserved
 * @author      Mautic
 *
 * @link        http://mautic.org
 *
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace MauticPlugin\MauticRecommenderBundle\Entity;

use Doctrine\DBAL\Types\Type;
use Doctrine\ORM\Mapping as ORM;
use Mautic\ApiBundle\Serializer\Driver\ApiMetadataDriver;
use Mautic\CoreBundle\Doctrine\Mapping\ClassMetadataBuilder;
use Mautic\CoreBundle\Entity\FiltersEntityTrait;

class Recommender
{
    use FiltersEntityTrait;

    /**
     * @var int
     */
    protected $id;

    /**
     * @var string
     */
    protected $name;

    /** @var array */
    protected $properties = [];

    /**
     * @var string
     */
    protected $filter;

    /** @var RecommenderTemplate */
    protected $template;

    /**
     * @var \DateTime
     */
    protected $dateAdded;

    /** @var array */
    protected $tableOrder;

    /**
     * @var string
     */
    protected $filterTarget;

    /**
     * @var int
     */
    private $numberOfItems = 9;

    /**
     * @var \Mautic\CategoryBundle\Entity\Category
     **/
    private $category;

    public function __construct()
    {
        $this->setDateAdded(new \DateTime());
    }

    public static function loadMetadata(ORM\ClassMetadata $metadata)
    {
        $builder = new ClassMetadataBuilder($metadata);
        $builder->setTable('recommender')
            ->setCustomRepositoryClass(RecommenderRepository::class)
            ->addId()
            ->addNamedField('name', Type::STRING, 'name')
            ->addNamedField('filter', Type::STRING, 'filter', true)
            ->addNamedField('filterTarget', Type::STRING, 'filter_target', true)
            ->addNamedField('dateAdded', Type::DATETIME, 'date_added');

        $builder->addCategory();

        $builder->createField('numberOfItems', Type::INTEGER)
            ->columnName('number_of_items')
            ->nullable()
            ->build();

        $builder->createField('tableOrder', Type::TARRAY)
            ->columnName('table_order')
            ->nullable()
            ->build();

        $builder->createField('properties', Type::TARRAY)
            ->columnName('properties')
            ->nullable()
            ->build();

        $builder->createManyToOne(
            'template',
            'MauticPlugin\MauticRecommenderBundle\Entity\RecommenderTemplate'
        )->addJoinColumn('template_id', 'id', true, false, 'CASCADE')->build();
        self::addFiltersMetadata($builder);
    }

    /**
     * Prepares the metadata for API usage.
     *
     * @param $metadata
     */
    public static function loadApiMetadata(ApiMetadataDriver $metadata)
    {
        $metadata->setGroupPrefix('import')
            ->addListProperties(
                [
                    'id',
                    'name',
                    'filter',
                    'properties',
                    'filters',
                    'template',
                    'dateAdded',
                    'numberOfItems',
                    'category',
                ]
            )
            ->build();
    }

    /**
     * Get id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name.
     *
     * @param string $name
     *
     * @return Recommender
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set dateAdded.
     *
     * @param \DateTime $dateAdded
     *
     * @return LeadEventLog
     */
    public function setDateAdded($dateAdded)
    {
        $this->dateAdded = $dateAdded;

        return $this;
    }

    /**
     * Get dateAdded.
     *
     * @return \DateTime
     */
    public function getDateAdded()
    {
        return $this->dateAdded;
    }

    /**
     * @param string $filter
     *
     * @return Recommender
     */
    public function setFilter($filter)
    {
        $this->filter = $filter;

        return $this;
    }

    /**
     * @return string
     */
    public function getFilter()
    {
        return $this->filter;
    }

    /**
     * @param RecommenderTemplate $template
     *
     * @return Recommender
     */
    public function setTemplate($template)
    {
        $this->template = $template;

        return $this;
    }

    /**
     * @return RecommenderTemplate
     */
    public function getTemplate()
    {
        return $this->template;
    }

    public function getCreatedBy()
    {
    }

    public function getHeader()
    {
    }

    public function getPublishStatus()
    {
    }

    public function isChanged()
    {
    }

    /**
     * @param array $properties
     *
     * @return Recommender
     */
    public function setProperties($properties)
    {
        $this->properties = $properties;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getProperties()
    {
        return $this->properties;
    }

    /**
     * @param int $numberOfItems
     *
     * @return Recommender
     */
    public function setNumberOfItems($numberOfItems)
    {
        $this->numberOfItems = $numberOfItems;

        return $this;
    }

    /**
     * @return int
     */
    public function getNumberOfItems()
    {
        return $this->numberOfItems;
    }

    /**
     * @param array $tableOrder
     *
     * @return Recommender
     */
    public function setTableOrder($tableOrder)
    {
        $this->tableOrder = $tableOrder;

        return $this;
    }

    /**
     * @return array
     */
    public function getTableOrder()
    {
        return $this->tableOrder;
    }

    /**
     * @param string $filter
     *
     * @return Recommender
     */
    public function setFilterTarget($filterTarget)
    {
        $this->filterTarget = $filterTarget;

        return $this;
    }

    /**
     * @return string
     */
    public function getFilterTarget()
    {
        return $this->filterTarget;
    }

    /**
     * @return \Mautic\CategoryBundle\Entity\Category
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * @param \Mautic\CategoryBundle\Entity\Category $category
     *
     * @return $this
     */
    public function setCategory($category)
    {
        $this->category = $category;

        return $this;
    }
}
