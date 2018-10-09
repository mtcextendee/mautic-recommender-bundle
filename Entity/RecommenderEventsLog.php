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
 * Class RecommenderEventsLog
 * @package MauticPlugin\MauticRecommenderBundle\Entity
 */
class RecommenderEventsLog
{
    /**
     * @var int
     */
    protected $id;

    /**
     * @var Lead
     */
    protected $lead;

    /**
     * @var Item
     */
    // protected $item_id;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var \DateTime
     */
    protected $dateAdded;

    /**
     * @var array
     */
    private $properties = [];

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
        $builder->setTable('recommender_event_log')
            ->setCustomRepositoryClass(RecommenderEventsLogRepository::class)
            ->addIndex(['lead_id'], 'lead_id_index')
          //  ->addIndex(['item_id'], 'item_id_index')
            ->addId()
            ->addNamedField('name', Type::STRING, 'name')
            ->addNamedField('dateAdded', Type::DATETIME, 'date_added')
            ->addNullableField('properties', Type::JSON_ARRAY);

        $builder->createManyToOne('lead', Lead::class)
            ->addJoinColumn('lead_id', 'id', true, false, 'CASCADE')
            ->inversedBy('eventLog')
            ->build();
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
                    'leadId',
                    'name',
                    'dateAdded',
                    'properties',
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
     * Set lead.
     *
     * @param Lead $lead
     *
     * @return LeadEventLog
     */
    public function setLead(Lead $lead)
    {
        $this->lead = $lead;

        return $this;
    }

    /**
     * Get lead.
     *
     * @return Lead
     */
    public function getLead()
    {
        return $this->lead;
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
     * Set properties.
     *
     * @param array $properties
     *
     * @return LeadEventLog
     */
    public function setProperties(array $properties)
    {
        $this->properties = $properties;

        return $this;
    }

    /**
     * Set one property into the properties array.
     *
     * @param string $key
     * @param string $value
     *
     * @return LeadEventLog
     */
    public function addProperty($key, $value)
    {
        $this->properties[$key] = $value;

        return $this;
    }

    /**
     * Get properties.
     *
     * @return array
     */
    public function getProperties()
    {
        return $this->properties;
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
}
