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

class ItemPropertyValue
{
    /**
     * @var int
     */
    protected $id;

    /**
     * @var ItemProperty
     */
    protected $property;

    /**
     * @var EventLog
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
            ->setCustomRepositoryClass(ItemPropertyValueRepository::class)
            ->addNamedField('value', Type::TEXT, 'value', false)
            ->addId();

        $builder->createManyToOne(
            'item',
            'MauticPlugin\MauticRecommenderBundle\Entity\Item'
        )->addJoinColumn('item_id', 'id', true, false, 'CASCADE')->build();


        $builder->createManyToOne(
            'property',
            'MauticPlugin\MauticRecommenderBundle\Entity\ItemProperty'
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
     * @return ItemProperty
     */
    public function getProperty()
    {
        return $this->property;
    }

    /**
     * @param ItemProperty $property
     */
    public function setProperty(ItemProperty $property)
    {
        $this->property = $property;
    }

    /**
     * @param EventLog $item
     *
     * @return ItemPropertyValue
     */
    public function setItem(EventLog $item)
    {
        $this->item = $item;

        return $this;
    }

    /**
     * @return EventLog
     */
    public function getItem()
    {
        return $this->item;
    }

    /**
     * @param string $value
     *
     * @return ItemPropertyValue
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

    /**
     * @param EventLog     $item
     * @param ItemProperty $property
     * @param string       $value
     */
    public function setValues(EventLog $item, ItemProperty $property, $value)
    {
        $this->item     = $item;
        $this->property = $property;
        $this->value    = $value;
    }
}
