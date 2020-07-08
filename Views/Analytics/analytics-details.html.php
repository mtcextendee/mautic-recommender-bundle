<?php

/*
 * @copyright   2014 Mautic Contributors. All rights reserved
 * @author      Mautic
 *
 * @link        http://mautic.org
 *
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */
?>
<div class="analytics-case">
    <div class="col-xs-12 va-m">
        <div class="panel">
            <div class="col-xs-12 mt-20">
                <h5 class="text-white dark-md fw-sb mb-xs">
                    <small class="pull-right">
                        <?php echo $view['translator']->trans('mautic.core.date.from'); ?>
                        <strong style="color:#666"><?php echo $dateFrom; ?></strong>
                        <?php echo $view['translator']->trans('mautic.core.date.to'); ?>
                        <strong style="color:#666"><?php echo $dateTo; ?></strong>
                    </small>
                    <span class="fa fa-line-chart"></span>
                    <?php echo $view['translator']->trans('mautic.plugin.recommender.google.analytics.overview'); ?>
                </h5>
            </div>


            <div class="panel-body box-layout">
                <div class="row">
                    <div class="col-xs-12 va-m mb-20">
                        <?php echo $view->render(
                            'MauticExtendeeAnalyticsBundle:Analytics:header.html.php',
                            ['tags' => $tags]
                        ); ?>
                    </div>

                    <?php echo $view->render(
                        'MauticExtendeeAnalyticsBundle:Analytics:data.html.php',
                        ['metrics' => $metrics]
                    ); ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    var CLIENT_ID = '<?php echo $keys['clientId'] ?>';
    var ids = 'ga:<?php echo $keys['viewId']; ?>';
    var metrics = '<?php echo implode(',', array_keys($rawMetrics)); ?>';
    var filters = '<?php echo $filters ?>';
    var currency = '<?php echo $keys['currency']; ?>';
    var dateFrom = '<?php echo (new \Mautic\CoreBundle\Helper\DateTimeHelper($dateFrom))->toLocalString(
        'Y-m-d'
    ); ?>';
    var dateTo = '<?php echo (new \Mautic\CoreBundle\Helper\DateTimeHelper($dateTo))->toLocalString('Y-m-d'); ?>';
    var metricsGraph = 'ga:sessions';
    <?php if (!empty($metrics['ecommerce'])) {
        ?>
    metricsGraph = metricsGraph + ',ga:transactions';
    <?php
    } ?>
    if (typeof analyticsReady == 'undefined') {
        var analyticsReady = false;
    }
</script>
<?php echo $view['assets']->includeScript(
    'plugins/MauticExtendeeAnalyticsBundle/Assets/js/analytics.js?time='.time()
); ?>
