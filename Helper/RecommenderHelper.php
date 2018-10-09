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
use MauticPlugin\MauticRecommenderBundle\Entity\Recommender;
use MauticPlugin\MauticRecommenderBundle\Service\RecommenderTokenFinder;
use MauticPlugin\MauticRecommenderBundle\Service\RecommenderTokenReplacer;
use Symfony\Component\Translation\TranslatorInterface;
use Mautic\PageBundle\Event\PageDisplayEvent;
use Mautic\PluginBundle\Helper\IntegrationHelper;
use MauticPlugin\MauticRecommenderBundle\Model\RecommenderModel;
use Recommender\RecommApi\Client;
use Recommender\RecommApi\Exceptions as Ex;
use Recommender\RecommApi\Requests as Reqs;

const NUM                   = 50;
const PROBABILITY_PURCHASED = 0.2;

/**
 * Class RecommenderHelper.
 */
class RecommenderHelper
{

    private $recommenderRegex = '{recommender=(.*?)}';

    /**
     * @var IntegrationHelper
     */
    protected $integrationHelper;

    /**
     * @var RecommenderModel $recommenderModel
     */
    protected $recommenderModel;

    /**
     * @var Translator
     */
    protected $translator;


    /**
     * @var Client
     */
    private $client;

    /**
     * @var CorePermissions
     */
    private $security;

    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * RecommenderHelper constructor.
     *
     * @param IntegrationHelper   $integrationHelper
     * @param RecommenderModel       $recommenderModel
     * @param TranslatorInterface $translator
     * @param CorePermissions     $security
     * @param EntityManager       $entityManager
     */
    public function __construct(
        IntegrationHelper $integrationHelper,
        RecommenderModel $recommenderModel,
        TranslatorInterface $translator,
        CorePermissions $security,
        EntityManager $entityManager
    ) {
        $this->integrationHelper = $integrationHelper;
        $this->recommenderModel     = $recommenderModel;
        $this->translator        = $translator;
        $this->security          = $security;
        $this->entityManager     = $entityManager;
    }

    /**
     * @return string
     */
    public function getRecommenderRegex()
    {
        return $this->recommenderRegex;
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
            ->setParameter('type', "recommender%")
            ->orderBy('e.id', 'DESC');

        return $q->execute()->fetchAll();
    }


}
