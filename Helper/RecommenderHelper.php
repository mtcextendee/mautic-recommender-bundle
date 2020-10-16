<?php

/*
 * @copyright   2016 Mautic Contributors. All rights reserved
 * @author      Mautic
 *
 * @link        http://mautic.org
 *
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace MauticPlugin\MauticRecommenderBundle\Helper;

use Doctrine\ORM\EntityManager;
use Mautic\CoreBundle\Security\Permissions\CorePermissions;
use Mautic\LeadBundle\Entity\Lead;
use Mautic\PageBundle\Event\PageDisplayEvent;
use Mautic\PluginBundle\Helper\IntegrationHelper;
use MauticPlugin\MauticRecommenderBundle\Entity\RecommenderTemplate;
use MauticPlugin\MauticRecommenderBundle\Model\TemplateModel;
use MauticPlugin\MauticRecommenderBundle\Service\RecommenderTokenFinder;
use MauticPlugin\MauticRecommenderBundle\Service\RecommenderTokenReplacer;
use Symfony\Component\Translation\TranslatorInterface;

const NUM                   = 50;
const PROBABILITY_PURCHASED = 0.2;

/**
 * Class RecommenderHelper.
 */
class RecommenderHelper
{
    public static $recommenderRegex = '{recommender=(.*?)}';

    /**
     * @var IntegrationHelper
     */
    protected $integrationHelper;

    /**
     * @var TemplateModel
     */
    protected $recommenderModel;

    /**
     * @var Translator
     */
    protected $translator;

    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * RecommenderHelper constructor.
     */
    public function __construct(
        IntegrationHelper $integrationHelper,
        TemplateModel $recommenderModel,
        TranslatorInterface $translator,
        CorePermissions $security,
        EntityManager $entityManager
    ) {
        $this->integrationHelper    = $integrationHelper;
        $this->recommenderModel     = $recommenderModel;
        $this->translator           = $translator;
        $this->entityManager        = $entityManager;
    }

    /**
     * @param $type
     *
     * @return string
     */
    public static function typeToTypeTranslator($type)
    {
        switch ($type) {
            case 'string':
            case 'float':
                return 'text';
            case 'set':
                return 'select';
            case 'boolean':
                return 'bool';
            case 'datetime':
                return 'date';
            default:
                return 'default';
        }
    }

    /**
     * @return array
     */
    public function getRecommenderEvents()
    {
        $q = $this->entityManager->getConnection()->createQueryBuilder();

        $q->select('e.id, e.name, e.type, e.campaign_id, e.channel, e.channel_id as channelId')
            ->from(MAUTIC_TABLE_PREFIX.'campaign_events', 'e')
            ->where(
                $q->expr()->like('e.type', ':type')
            )
            ->setParameter('type', 'recommender%')
            ->orderBy('e.id', 'DESC');

        return $q->execute()->fetchAll();
    }
}
