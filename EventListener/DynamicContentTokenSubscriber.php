<?php

/*
 * @copyright   2014 Mautic Contributors. All rights reserved
 * @author      Mautic
 *
 * @link        http://mautic.org
 *
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace MauticPlugin\MauticRecommenderBundle\EventListener;

use Doctrine\ORM\EntityManager;
use Mautic\CoreBundle\Event\TokenReplacementEvent;
use Mautic\CoreBundle\EventListener\CommonSubscriber;
use Mautic\DynamicContentBundle\DynamicContentEvents;
use MauticPlugin\MauticRecommenderBundle\EventListener\Service\CampaignLeadDetails;
use MauticPlugin\MauticRecommenderBundle\Service\RecommenderTokenReplacer;

/**
 * Class DynamicContentTokenSubscriber.
 */
class DynamicContentTokenSubscriber extends CommonSubscriber
{

    /**
     * @var RecommenderTokenReplacer
     */
    private $recommenderTokenReplacer;

    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @var CampaignLeadDetails
     */
    private $campaignLeadDetails;


    /**
     * @param RecommenderTokenReplacer $recommenderTokenReplacer
     * @param EntityManager         $entityManager
     *
     * @param CampaignLeadDetails   $campaignLeadDetails
     *
     * @internal param MessageQueueModel $messageQueueModel
     */
    public function __construct(
        RecommenderTokenReplacer $recommenderTokenReplacer,
        EntityManager $entityManager,
        CampaignLeadDetails $campaignLeadDetails
    ) {
        $this->recommenderTokenReplacer = $recommenderTokenReplacer;
        $this->entityManager         = $entityManager;
        $this->campaignLeadDetails   = $campaignLeadDetails;
    }


    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            DynamicContentEvents::TOKEN_REPLACEMENT => ['onDynamicContentTokenReplacement', 200],
        ];
    }



    /**
     * @param TokenReplacementEvent $event
     */
    public function onDynamicContentTokenReplacement(TokenReplacementEvent $event)
    {
        $clickthrough = $event->getClickthrough();
        $slot         = $clickthrough['slot'];
        $leadId       = $clickthrough['lead'];
        // Find last added campaign metadata
        $metadata = [];
        if ($results = $this->getMetadataFromLog('recommender-dynamic-content', null, $leadId, 99)) {
            foreach ($results as $result) {
                $metadata = unserialize($result['metadata']);
                if (!empty($metadata['slot']) && $metadata['slot'] == $slot) {
                    break;
                }
            }
        }
        if (empty($metadata['slot'])) {
            return;
        }

        $this->setContentByMetadata($event, $metadata, $leadId, $clickthrough);

    }


    /**
     * @param $event
     * @param $metadata
     * @param $leadId
     */
    private function setContentByMetadata($event, $metadata, $leadId, $clickthrough)
    {
        if (empty($metadata['type']) || empty($metadata['campaignId'])) {
            return;
        }
        $type       = $metadata['type'];
        $campaignId = $metadata['campaignId'];
        $content    = $event->getContent();
        $content    =
            $this->recommenderTokenReplacer->replaceTokensFromContent(
                $content,
                $this->getOptionsBasedOnRecommendationsType($type, $campaignId, $leadId),
                $clickthrough
            );

        $event->setContent($content);
    }


    private function getMetadataFromLog($channel, $channelId = null, $contactId, $limit = 1)
    {
        $q = $this->em->getConnection()->createQueryBuilder();

        $q->select('e.id, e.metadata')
            ->from(MAUTIC_TABLE_PREFIX.'campaign_lead_event_log', 'e')
            ->where(
                $q->expr()->eq('e.channel', ':channel'),
                $q->expr()->eq('e.lead_id', ':lead_id')
            )
            ->setParameter('channel', $channel)
            ->setParameter('lead_id', $contactId)
            ->orderBy('e.id', 'DESC')
            ->getMaxResults($limit);

        // find by channel ID, skip for dynamic content
        if ($channelId) {
            $q->andWhere($q->expr()->eq('e.channel_id', ':channel_id'))
                ->setParameter('channel_id', $channelId);
        }
        return $q->execute()->fetchAll();
    }


    /**
     * @param     $config
     * @param int $campaignId
     * @param int $leadId
     *
     * @return array
     */
    private function getOptionsBasedOnRecommendationsType(array $config, $campaignId, $leadId)
    {
        $options = [];

        $type = $config['type'];

        switch ($type) {
            case 'abandoned_cart':
                $seconds = $this->campaignLeadDetails->getDiffSecondsFromAddedTime($campaignId, $leadId);
                $options = $this->getAbandonedCartOptions(1, $seconds);
                break;
            case 'advanced':
                if (!empty($config['filter'])) {
                    $options['filter'] = $config['filter'];
                }
                if (!empty($config['booster'])) {
                    $options['booster'] = $config['booster'];
                }
                break;
        }

        return $options;
    }

    /**
     * @param $cartMinAge
     * @param $cartMaxAge
     *
     * @return array
     */
    private function getAbandonedCartOptions($cartMinAge, $cartMaxAge)
    {
        return [
            "expertSettings" => [
                "algorithmSettings" => [
                    "evaluator" => [
                        "name" => "reql",
                    ],
                    "model"     => [
                        "name"     => "reminder",
                        "settings" => [
                            "parameters" => [
                                "interaction-types"        => [
                                    "detail-view"   => [
                                        "enabled" => false,
                                    ],
                                    "cart-addition" => [
                                        "enabled" => true,
                                        "weight"  => 1.0,
                                        "min-age" => $cartMinAge,
                                        "max-age" => $cartMaxAge,
                                    ],
                                ],
                                "filter-purchased-max-age" => $cartMaxAge,
                            ],
                        ],
                    ],
                ],
            ],
        ];
    }


}
