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
 * Class Event.
 */
class Event
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

    /** @var int */
    protected $weight = 0;

    /**
     * @var \DateTime
     */
    protected $dateAdded;

    /**
     * @var int
     */
    protected $numberOfLogs;

    /**
     * @var string|null
     */
    protected $lastDateAdded;

    public function __construct()
    {
        $this->setDateAdded(new \DateTime());
    }

    public static function loadMetadata(ORM\ClassMetadata $metadata)
    {
        $builder = new ClassMetadataBuilder($metadata);
        $builder->setTable('recommender_event')
            ->setCustomRepositoryClass(EventRepository::class)
            ->addId()
            ->addNamedField('name', Type::STRING, 'name')
            ->addNamedField('weight', Type::INTEGER, 'weight')
            ->addNamedField('dateAdded', Type::DATETIME, 'date_added');

        $builder->createField('type', Type::STRING)
            ->columnName('type')
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
        $metadata->setGroupPrefix('import')
            ->addListProperties(
                [
                    'id',
                    'name',
                    'weight',
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
     * @param int $weight
     *
     * @return Event
     */
    public function setWeight($weight)
    {
        $this->weight = $weight;

        return $this;
    }

    /**
     * @return int
     */
    public function getWeight()
    {
        return $this->weight;
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

    /**
     * @param string $type
     *
     * @return Event
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

    public function getTypeTranslations()
    {
    }

    /**
     * @return int
     */
    public function getNumberOfLogs()
    {
        return $this->numberOfLogs;
    }

    /**
     * @param $numberOfLogs
     *
     * @return $this
     */
    public function setNumberOfLogs($numberOfLogs)
    {
        $this->numberOfLogs = $numberOfLogs;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getLastDateAdded(): ?string
    {
        return $this->lastDateAdded;
    }

    /**
     * @param string|null $lastDateAdded
     */
    public function setLastDateAdded(?string $lastDateAdded): void
    {
        $this->lastDateAdded = $lastDateAdded;
    }
}
