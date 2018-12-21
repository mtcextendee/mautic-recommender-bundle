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

    /** @var  array */
    protected $properties;

    /**
     * @var string
     */
    protected $filter;

    /** @var  RecommenderTemplate */
    protected $template;

    /**
     * @var \DateTime
     */
    protected $dateAdded;

    public function __construct()
    {
        $this->setDateAdded(new \DateTime());
    }

    /**
     * @param ORM\ClassMetadata $metadata
     */
    public static function loadMetadata(ORM\ClassMetadata $metadata)
    {
        $builder = new ClassMetadataBuilder($metadata);
        $builder->setTable('recommender')
            ->setCustomRepositoryClass(RecommenderRepository::class)
            ->addId()
            ->addNamedField('name', Type::STRING, 'name')
            ->addNamedField('filter', Type::STRING, 'filter')
            ->addNullableField('properties', 'json_array')
            ->addNamedField('dateAdded', Type::DATETIME, 'date_added');

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
}
