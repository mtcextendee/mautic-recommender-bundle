<?php

/*
 * @copyright   2016 Mautic Contributors. All rights reserved
 * @author      Mautic
 *
 * @link        http://mautic.org
 *
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

/** @var \MauticPlugin\MauticRecommenderBundle\Entity\RecommenderTemplate $entity */
$isEmbedded = false;
if (!$isEmbedded) {
    $view->extend('MauticCoreBundle:Default:content.html.php');
}
$view['slots']->set('headerTitle', $entity->getName());


$customButtons = [];
if (!$isEmbedded) {
    $view['slots']->set(
        'actions',
        $view->render(
            'MauticCoreBundle:Helper:page_actions.html.php',
            [
                'item' => $entity,
                'customButtons' => (isset($customButtons)) ? $customButtons : [],
                'templateButtons' => [
                    'edit' => $view['security']->hasEntityAccess(
                        $permissions['recommender:recommender:editown'],
                        $permissions['recommender:recommender:editother'],
                        $entity->getCreatedBy()
                    ),
                    'clone' => $permissions['recommender:recommender:create'],
                    'delete' => $view['security']->hasEntityAccess(
                        $permissions['recommender:recommender:deleteown'],
                        $permissions['recommender:recommender:deleteother'],
                        $entity->getCreatedBy()
                    ),
                    'close' => $view['security']->hasEntityAccess(
                        $permissions['recommender:recommender:viewown'],
                        $permissions['recommender:recommender:viewother'],
                        $entity->getCreatedBy()
                    ),
                ],
                'routeBase' => 'recommender_event',
            ]
        )
    );
    $view['slots']->set(
        'publishStatus',
        $view->render('MauticCoreBundle:Helper:publishstatus_badge.html.php', ['entity' => $entity])
    );
}
?>

<!-- start: box layout -->
<div class="box-layout">
    <!-- left section -->
    <div class="col-md-9 bg-white height-auto">
        <div class="bg-auto">
            <!-- page detail header -->
            <div class="pr-md pl-md pt-lg pb-lg">
                <div class="box-layout">
                    <div class="col-xs-10">
                    </div>
                </div>
            </div>
            <!--/ page detail header -->
            <!-- page detail collapseable -->
            <div class="collapse" id="page-details">
                <div class="pr-md pl-md pb-md">
                    <div class="panel shd-none mb-0">
                        <table class="table table-bordered table-striped mb-0">
                            <tbody>
                            <?php echo $view->render(
                                'MauticCoreBundle:Helper:details.html.php',
                                ['entity' => $entity]
                            ); ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <!--/ page detail collapseable -->
        </div>
        <div class="bg-auto bg-dark-xs">
            <!-- page detail collapseable toggler -->
            <div class="hr-expand nm">
                <span data-toggle="tooltip" title="Detail">
                    <a href="javascript:void(0)" class="arrow text-muted collapsed" data-toggle="collapse"
                       data-target="#page-details">
                        <span class="caret"></span> <?php echo $view['translator']->trans('mautic.core.details'); ?>
                    </a>
                </span>
            </div>
            <!--/ page detail collapseable toggler -->


            <!--/ left section -->

            <!-- right section -->
            <div class="col-md-3 bg-white bdr-l height-auto">
                <hr class="hr-w-2" style="width:50%">
            </div>
            <!--/ right section -->
        </div>

        <!--/ end: box layout -->
