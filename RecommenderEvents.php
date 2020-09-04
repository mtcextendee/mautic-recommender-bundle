<?php

/*
 * @copyright   2014 Mautic Contributors. All rights reserved
 * @author      Mautic
 *
 * @link        http://mautic.org
 *
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace MauticPlugin\MauticRecommenderBundle;

/**
 * Class RecommenderEvent
 * Events available for MauticRecommenderBundle.
 */
final class RecommenderEvents
{
    /**
     * The mautic.recommender_pre_save event is thrown right before a asset is persisted.
     *
     * The event listener receives a
     * MauticPlugin\MauticRecommenderBundle\Event\RecommenderEvent instance.
     *
     * @var string
     */
    const PRE_SAVE = 'mautic.recommender_pre_save';

    /**
     * The mautic.recommender_post_save event is thrown right after a asset is persisted.
     *
     * The event listener receives a
     * MauticPlugin\MauticRecommenderBundle\Event\RecommenderEvent instance.
     *
     * @var string
     */
    const POST_SAVE = 'mautic.recommender_post_save';

    /**
     * The mautic.recommender_pre_delete event is thrown prior to when a asset is deleted.
     *
     * The event listener receives a
     * MauticPlugin\MauticRecommenderBundle\Event\RecommenderEvent instance.
     *
     * @var string
     */
    const PRE_DELETE = 'mautic.recommender_pre_delete';

    /**
     * The mautic.recommender_post_delete event is thrown after a asset is deleted.
     *
     * The event listener receives a
     * MauticPlugin\MauticRecommenderBundle\Event\RecommenderEvent instance.
     *
     * @var string
     */
    const POST_DELETE = 'mautic.recommender_post_delete';

    /**
     * The mautic.email.on_campaign_trigger_action event is fired when the campaign action triggers.
     *
     * The event listener receives a
     * Mautic\CampaignBundle\Event\CampaignExecutionEvent
     *
     * @var string
     */
    const ON_CAMPAIGN_TRIGGER_ACTION = 'mautic.plugin.recommender.on_campaign_trigger_action';

    /**
     * The mautic.email.on_campaign_trigger_action event is fired when the campaign action triggers.
     *
     * The event listener receives a
     * Mautic\CampaignBundle\Event\CampaignExecutionEvent
     *
     * @var string
     */
    const ON_CAMPAIGN_TRIGGER_CONDITION = 'mautic.plugin.recommender.on_campaign_trigger_condition';

    /**
     * The mautic.plugin.recommender.on_campaign_trigger_decision event is fired when the campaign decision is fired.
     *
     * The event listener receives a
     * Mautic\CampaignBundle\Event\CampaignExecutionEvent
     *
     * @var string
     */
    const ON_CAMPAIGN_TRIGGER_DECISION                = 'mautic.plugin.recommender.on_campaign_trigger_decision';

    const ON_RECOMMENDER_FORM_FILTER_GENERATE         = 'mautic.plugin.recommender.in_filter_form_display';
    const ON_RECOMMENDER_FILTER_FORM_CHOICES_GENERATE = 'mautic.plugin.recommender.in_filter_form_choices_generate';
    const ON_RECOMMENDER_FILTER_RESULTS               = 'mautic.plugin.recommender.filter_results';
    const ON_RECOMMENDER_EVENT_SENT                   = 'mautic.plugin.recommender.before_send_event';
    const LIST_FILTERS_ON_FILTERING                   = 'mautic.plugin.recommender.filters_filtering';

    /**
     * The mautic.plugin.recommender.on_recommender_build_query event is fired when the recommender query is build.
     *
     * The event listener receives a
     * MauticPlugin\MauticRecommenderBundle\Event\RecommenderQueryBuildEvent
     *
     * @var string
     */
    const ON_RECOMMENDER_BUILD_QUERY = 'mautic.plugin.recommender.on_recommender_build_query';
}
