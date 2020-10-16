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
use Mautic\CoreBundle\Helper\DateTimeHelper;
use Mautic\LeadBundle\Entity\Lead;

class EventLog
{
    /**
     * @var int
     */
    protected $id;

    /**
     * @var Item
     */
    protected $item;

    /**
     * @var Event
     */
    protected $event;

    /**
     * @var Lead
     */
    protected $lead;

    /*
     * @var \DateTime
     */
    protected $dateAdded;

    public function __construct()
    {
        $this->setDateAdded(new \DateTime());
    }

    public static function loadMetadata(ORM\ClassMetadata $metadata)
    {
        $builder = new ClassMetadataBuilder($metadata);
        $builder->setTable('recommender_event_log')
            ->setCustomRepositoryClass(EventLogRepository::class)
            ->addIndex(['item_id'], 'item_id_index')
            ->addIndex(['event_id'], 'event_id_index')
            ->addIndex(['date_added'], 'date_added_index')
            ->addId()
            ->addNamedField('dateAdded', 'datetime', 'date_added');

        $builder->createManyToOne(
            'event',
            'MauticPlugin\MauticRecommenderBundle\Entity\Event'
        )->addJoinColumn('event_id', 'id', true, false, 'CASCADE')->build();

        $builder->createManyToOne(
            'item',
            'MauticPlugin\MauticRecommenderBundle\Entity\Item'
        )->addJoinColumn('item_id', 'id', true, false, 'CASCADE')->build();

        $builder->createManyToOne(
            'lead',
            'Mautic\LeadBundle\Entity\Lead'
        )->addJoinColumn('lead_id', 'id', true, false, 'SET NULL')
            ->cascadePersist()
            ->build();
    }

    /**
     * Prepares the metadata for API usage.
     *
     * @param $metadata
     */
    public static function loadApiMetadata(ApiMetadataDriver $metadata)
    {
        $metadata->setGroupPrefix('event_log')
            ->addListProperties(
                [
                    'id',
                    'item_id',
                    'event_id',
                    'lead_id',
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
     * @return EventLog
     */
    public function setItem(Item $item)
    {
        $this->item = $item;

        return $this;
    }

    /**
     * @return string
     */
    public function getItem()
    {
        return $this->item;
    }

    /**
     * @param \DateTime|string $dateAdded
     *
     * @return EventLog
     */
    public function setDateAdded($dateAdded)
    {
        if (!$dateAdded instanceof \DateTime) {
            $dateAdded = (new DateTimeHelper($dateAdded))->getDateTime();
        }
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
     * @return EventLog
     */
    public function setEvent(Event $event)
    {
        $this->event = $event;

        return $this;
    }

    /**
     * @return Event
     */
    public function getEvent()
    {
        return $this->event;
    }

    /**
     * @return EventLog
     */
    public function setLead(Lead $lead)
    {
        $this->lead = $lead;

        return $this;
    }

    /**
     * @return Lead
     */
    public function getLead()
    {
        return $this->lead;
    }
}
