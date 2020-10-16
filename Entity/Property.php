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

class Property
{
    /**
     * @var int
     */
    protected $id;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $type;

    /**
     * @var bool
     */
    protected $segmentFilter = false;

    /**
     * @var string
     */
    protected $value;

    /**
     * @var \DateTime
     */
    protected $dateAdded;

    /**
     * @var string
     */
    protected $propertyType;

    public function __construct()
    {
        $this->setDateAdded(new \DateTime());
    }

    public static function loadMetadata(ORM\ClassMetadata $metadata)
    {
        $builder = new ClassMetadataBuilder($metadata);
        $builder->setTable('recommender_property')
            ->setCustomRepositoryClass(PropertyRepository::class)
            ->addIndex(['name'], 'name_index')
            ->addId()
            ->addNamedField('name', 'string', 'name')
            ->addNamedField('type', 'string', 'type')
            ->addNamedField('dateAdded', Type::DATETIME, 'date_added');
        $builder->addNullableField('segmentFilter', Type::BOOLEAN, 'segment_filter');

        $builder->createField('propertyType', Type::STRING)
            ->columnName('property_type')
            ->length(20)
            ->nullable()
            ->build();
    }

    /**
     * Prepares the metadata for API usage.
     *
     * @param $metadata
     */
    public static function loadApiMetadata(ApiMetadataDriver $metadata)
    {
        $metadata->setGroupPrefix('item')
            ->addListProperties(
                [
                    'id',
                    'name',
                    'type',
                    'dateAdded',
                    'propertyType',
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
     * @return LeadEventLog
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
     * @param string $value
     *
     * @return Property
     */
    public function setValue($value)
    {
        $this->value = $value;

        return $this;
    }

    /**
     * Get value.
     *
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @param string $type
     *
     * @return Item
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @return Item
     */
    public function setDateAdded(\DateTime $dateAdded)
    {
        $this->dateAdded = $dateAdded;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getDateAdded()
    {
        return $this->dateAdded;
    }

    /**
     * @return Property
     */
    public function setSegmentFilter(bool $segmentFilter)
    {
        $this->segmentFilter = $segmentFilter;

        return $this;
    }

    /**
     * @return bool
     */
    public function isSegmentFilter()
    {
        return $this->segmentFilter;
    }

    /**
     * @param string $propertyType
     *
     * @return Property
     */
    public function setPropertyType($propertyType)
    {
        $this->propertyType = $propertyType;

        return $this;
    }

    /**
     * @return string
     */
    public function getPropertyType()
    {
        return $this->propertyType;
    }
}
