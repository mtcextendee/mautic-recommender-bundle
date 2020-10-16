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
     * @var Property
     */
    protected $property;

    /**
     * @var Item
     */
    protected $item;

    /**
     * @var mixed
     */
    protected $value;

    private $changes = [];

    public static function loadMetadata(ORM\ClassMetadata $metadata)
    {
        /** @var ClassMetadataBuilder $builder */
        $builder = new ClassMetadataBuilder($metadata);
        $builder->setTable('recommender_item_property_value')
            ->setCustomRepositoryClass(ItemPropertyValueRepository::class)
            ->addnamedfield('value', Type::TEXT, 'value')
            //->addIndex(['value'], 'value')
            ->addId();

        $builder->createManyToOne(
            'item',
            'MauticPlugin\MauticRecommenderBundle\Entity\Item'
        )->addJoinColumn('item_id', 'id', true, false, 'CASCADE')->build();

        $builder->createManyToOne(
            'property',
            'MauticPlugin\MauticRecommenderBundle\Entity\Property'
        )->addJoinColumn('property_id', 'id', true, false, 'CASCADE')->build();
    }

    /**
     * Prepares the metadata for API usage.
     *
     * @param $metadata
     */
    public static function loadApiMetadata(ApiMetadataDriver $metadata)
    {
        $metadata->setGroupPrefix('value')
            ->addListProperties(
                [
                    'id',
                    'name',
                    'item',
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
     * @return Property
     */
    public function getProperty()
    {
        return $this->property;
    }

    public function setProperty(Property $property)
    {
        $this->property = $property;
    }

    /**
     * @return ItemPropertyValue
     */
    public function setItem(Item $item)
    {
        $this->item = $item;

        return $this;
    }

    /**
     * @return Item
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
    public function setValue($value)
    {
        if ($this->value != $value) {
            $this->changes['value'] = isset($this->value) ? $this->value : '';
        }
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
     * @param string $value
     */
    public function setValues(Item $item, Property $property, $value)
    {
        $this->item     = $item;
        $this->property = $property;
        $this->value    = $value;
    }

    public function isChanged($key)
    {
        if (isset($this->changes[$key])) {
            if (empty($this->changes[$key])) {
                return true;
            }

            return $this->changes[$key];
        }

        return false;
    }
}
