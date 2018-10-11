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

/**
 * Class RecommenderItem
 * @package MauticPlugin\MauticRecommenderBundle\Entity
 */
class RecommenderItem
{
    /**
     * @var int
     */
    protected $id;

    /**
     * @var string
     */
    protected $itemId;

    /*
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
        $builder->setTable('recommender_item')
            ->setCustomRepositoryClass(RecommenderItemRepository::class)
            ->addIndex(['item_id'], 'item_id_index')
            ->addId()
            ->addNamedField('itemId', 'string', 'item_id', true)
            ->addNamedField('dateAdded', 'datetime', 'date_added');
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
                    'itemId',
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
     * @param string $itemId
     *
     * @return RecommenderItem
     */
    public function setItemId(string $itemId)
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
     * @return RecommenderItem
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
}
