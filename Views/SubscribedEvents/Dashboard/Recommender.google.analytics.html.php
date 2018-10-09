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
<div id="demo" class="demo"></div>

<div class="col-sm-12">
    <div class="panel">
        <div class="panel-body box-layout">
            <div class="col-xs-12 va-m">
                <div class="row">
                    <?php foreach ($tags as $utm => $value) { ?>
                        <div class="col-xs-12 col-sm-3">

                            <span class="label" style="background-color:#787a7a"><?php echo $utm; ?></span>
                            &nbsp;<small><?php echo $value; ?></small>
                        </div>
                        <?php
                    }
                    ?>
                </div>
                <hr>
                <h5 class="text-white dark-md fw-sb mb-xs">
                    <small class="pull-right">
                        <?php echo $view['translator']->trans('mautic.core.date.from'); ?>
                        <strong style="color:#666"><?php echo $dateFrom; ?></strong>
                        <?php echo $view['translator']->trans('mautic.core.date.to'); ?>
                        <strong style="color:#666"><?php echo $dateTo; ?></strong>
                    </small>
                    <span class="fa fa-line-chart"></span>
                    <?php echo $view['translator']->trans('plugin.extendee.analytics.overview'); ?>
                    <i id="analytics-loading" class="fa fa-spin fa-spinner"></i>
                </h5>
            </div>
        </div>

        <div id="analytics-auth" class="clearfix row pt-0 pl-15 pb-15 pr-15 " style="display: none">
            <div class="col-sm-4 col-xs-6">
                <section id="auth-button"></section>
            </div>
        </div>

        <div id="eanalytics-stats-no-results" class="clearfix" style="display: none">
            <div class="col-xs-12">
                <div class="alert alert-info">
                    <?php echo $view['translator']->trans('plugin.extendee.analytics.no.results'); ?>
                </div>
            </div>
        </div>
        <div id="eanalytics-stats" class="clearfix pb-15" style="display: none">
            <?php
            if (!empty($metrics['overview'])) {
                foreach ($metrics['overview'] as $metric => $label) {
                    ?>
                    <div class="col-sm-4 col-xs-6">
                        <h3 class="pull-left"><span class="label label-primary"
                                                    id="<?php echo $metric; ?>"></span></h3>
                        <span class="mt-5 pt-2 ml-10 pull-left"><?php echo $label; ?></span>
                    </div>
                    <?php
                }
            }
            ?>


            <?php
            if (!empty($metrics['ecommerce'])) {
                ?>
                <div class="panel-body box-layout pt-20">
                    <div class="col-xs-12 va-m">
                        <h5 class="text-white dark-md fw-sb mb-xs">
                            <span class="fa fa-line-chart"></span>
                            <?php echo $view['translator']->trans('plugin.extendee.analytics.ecommerce'); ?>
                        </h5>
                    </div>
                </div>
                <?php
                foreach ($metrics['ecommerce'] as $metric => $label) {
                    ?>
                    <div class="col-sm-4 col-xs-6">
                        <h3 class="pull-left"><span class="label label-success"
                                                    id="<?php echo $metric; ?>"></span></h3>
                        <span class="mt-5 pt-2 ml-10 pull-left"><?php echo $label; ?></span>
                    </div>
                    <?php
                }
            }
            ?>

            <?php
            if (!empty($metrics['goals'])) {
                ?>
                <div class="panel-body box-layout pt-20">
                    <div class="col-xs-4 va-m">
                        <h5 class="text-white dark-md fw-sb mb-xs">
                            <span class="fa fa-line-chart"></span>
                            <?php echo $view['translator']->trans('plugin.extendee.analytics.goals'); ?>
                        </h5>
                    </div>
                    <div class="col-xs-8 va-m">
                    </div>
                </div>
                <?php
                foreach ($metrics['goals'] as $metric => $label) {
                    ?>
                    <div class="col-sm-4 col-xs-6">
                        <h3 class="pull-left"><span class="label label-info"
                                                    id="<?php echo $metric; ?>"></span></h3>
                        <span class="mt-5 pt-2 ml-10 pull-left"><?php echo $label; ?></span>
                    </div>
                    <?php
                }
            }
            ?>
            <div class="panel-body box-layout pt-20 pb-0">
                <div class="col-xs-4 va-m">
                    <h5 class="text-white dark-md fw-sb mb-xs">
                        <span class="fa fa-line-chart"></span>
                        <?php echo $view['translator']->trans('plugin.extendee.analytics.graph.by.days'); ?>
                    </h5>
                </div>
                <div class="col-xs-8 va-m">
                </div>
            </div>
            <div class="col-xs-12">
                <div id="chart-container"></div>
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
    var dateFrom = '<?php echo (new \Mautic\CoreBundle\Helper\DateTimeHelper($dateFrom))->toLocalString('Y-m-d'); ?>';
    var dateTo = '<?php echo (new \Mautic\CoreBundle\Helper\DateTimeHelper($dateTo))->toLocalString('Y-m-d'); ?>';
    var metricsGraph = 'ga:sessions';
    <?php if (!empty($metrics['ecommerce'])) { ?>
    metricsGraph = metricsGraph+',ga:transactions';
    <?php } ?>
    if (typeof analyticsReady == 'undefined') {
        var analyticsReady = false;
    }
</script>
<?php echo $view['assets']->includeScript(
        'plugins/MauticRecommenderBundle/Assets/js/analytics.js?time='.time()
); ?>

