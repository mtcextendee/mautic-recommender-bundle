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
use Mautic\LeadBundle\Entity\Lead;

class Item
{
    /**
     * @var int
     */
    protected $id;

    /**
     * @var
     */
    protected $itemId;

    /*
     * @var \DateTime
     */
    protected $dateAdded;

    /*
   * @var \DateTime
   */
    protected $dateModified;


    public function __construct()
    {
        $this->setDateAdded(new \DateTime());
        $this->setDateModified(new \DateTime());
    }

    /**
     * @param ORM\ClassMetadata $metadata
     */
    public static function loadMetadata(ORM\ClassMetadata $metadata)
    {
        $builder = new ClassMetadataBuilder($metadata);
        $builder->setTable('recommender_item')
            ->setCustomRepositoryClass(ItemRepository::class)
            ->addIndex(['item_id'], 'item_id_index')
            ->addId()
            ->addNamedField('itemId', 'string', 'item_id')
            ->addNamedField('dateAdded', 'datetime', 'date_added')
            ->addNamedField('dateModified', 'datetime', 'date_modified');


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
                    'item_id',
                    'dateAdded',
                    'dateModified',
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
     * @param string $itemId
     *
     * @return Item
     */
    public function setItemId($itemId)
    {
        $this->itemId = $itemId;

        return $this;
    }

    /**
     * @return string
     */
    public function getItemId()
    {
        return $this->itemId;
    }

    /**
     * @param \DateTime $dateAdded
     *
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
     * @param mixed $dateModified
     *
     * @return Item
     */
    public function setDateModified($dateModified)
    {
        $this->dateModified = $dateModified;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getDateModified()
    {
        return $this->dateModified;
    }
}
