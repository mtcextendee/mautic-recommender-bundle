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

/**
 * Class RecommenderItemPropertyValue
 *
 * @package MauticPlugin\MauticRecommenderBundle\Entity
 */
class RecommenderItemPropertyValue
{
    /**
     * @var int
     */
    protected $id;

    /**
     * @var RecommenderItemProperty
     */
    protected $property;

    /**
     * @var RecommenderItem
     */
    protected $item;

    /**
     * @var string
     */
    protected $value;


    /**
     * @param ORM\ClassMetadata $metadata
     */
    public static function loadMetadata(ORM\ClassMetadata $metadata)
    {
        $builder = new ClassMetadataBuilder($metadata);
        $builder->setTable('recommender_item_property_value')
            ->setCustomRepositoryClass(RecommenderItemPropertyValueRepository::class)
            ->addNamedField('value', Type::TEXT, 'value', false)
            ->addId();

        $builder->createManyToOne(
            'item',
            'MauticPlugin\MauticRecommenderBundle\Entity\RecommenderItem'
        )->addJoinColumn('item_id', 'id', true, false, 'CASCADE')->build();


        $builder->createManyToOne(
            'property',
            'MauticPlugin\MauticRecommenderBundle\Entity\RecommenderItemProperty'
        )->addJoinColumn('property_id', 'id', true, false, 'CASCADE')->build();

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
                    'value',
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
     * @return RecommenderItemProperty
     */
    public function getProperty()
    {
        return $this->property;
    }

    /**
     * @param RecommenderItemProperty $property
     */
    public function setProperty(RecommenderItemProperty $property)
    {
        $this->property = $property;
    }

    /**
     * @param RecommenderItem $item
     *
     * @return RecommenderItemPropertyValue
     */
    public function setItem(RecommenderItem $item)
    {
        $this->item = $item;

        return $this;
    }

    /**
     * @return RecommenderItem
     */
    public function getItem()
    {
        return $this->item;
    }

    /**
     * @param string $value
     *
     * @return RecommenderItemPropertyValue
     */
    public function setValue(string $value)
    {
        $this->value = $value;

        return $this;
    }

    /**
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }
}
