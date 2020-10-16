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

class EventLogValue
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
     * @var EventLog
     */
    protected $eventLog;

    /**
     * @var mixed
     */
    protected $value;

    public static function loadMetadata(ORM\ClassMetadata $metadata)
    {
        $builder = new ClassMetadataBuilder($metadata);
        $builder->setTable('recommender_event_log_property_value')
            ->setCustomRepositoryClass(EventLogValueRepository::class)
            ->addNamedField('value', Type::STRING, 'value', false)
            ->addIndex(['value'], 'value')
            ->addId();

        $builder->createManyToOne(
            'eventLog',
            'MauticPlugin\MauticRecommenderBundle\Entity\EventLog'
        )->addJoinColumn('event_log_id', 'id', true, false, 'CASCADE')->build();

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
                    'eventLog',
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
    public function setValue(string $value = null)
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
     * @param string $value
     */
    public function setValues(EventLog $eventLog, Property $property, $value)
    {
        $this->eventLog     = $eventLog;
        $this->property     = $property;
        $this->value        = $value;
    }

    public function setEventLog(EventLog $eventLog): EventLogValue
    {
        $this->eventLog = $eventLog;

        return $this;
    }

    public function getEventLog(): EventLog
    {
        return $this->eventLog;
    }
}
