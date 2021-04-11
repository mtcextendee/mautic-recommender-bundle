<?php

/*
 * @copyright   2014 Mautic Contributors. All rights reserved
 * @author      Mautic
 *
 * @link        http://mautic.org
 *
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */
if ('index' == $tmpl) {
    $view->extend('MauticRecommenderBundle:Recommender:index.html.php');
}
/* @var \MauticPlugin\MauticRecommenderBundle\Entity\RecommenderTemplate[] $items */
?>
<?php if (count($items)): ?>
    <div class="table-responsive page-list">
        <table class="table table-hover table-striped table-bordered msgtable-list" id="msgTable">
            <thead>
            <tr>
                <?php
                echo $view->render(
                    'MauticCoreBundle:Helper:tableheader.html.php',
                    [
                        'checkall'        => 'true',
                        'target'          => '#msgTable',
                        'routeBase'       => 'recommender',
                        'templateButtons' => [
                            'delete' => $permissions['recommender:recommender:deleteown']
                                || $permissions['recommender:recommender:deleteother'],
                        ],
                    ]
                );

                echo $view->render(
                    'MauticCoreBundle:Helper:tableheader.html.php',
                    [
                        'sessionVar' => 'recommender',
                        'orderBy'    => 'e.name',
                        'text'       => 'mautic.core.name',
                        'class'      => 'col-msg-name',
                        'default'    => true,
                    ]
                );

                echo $view->render(
                    'MauticCoreBundle:Helper:tableheader.html.php',
                    [
                        'sessionVar' => 'recommender',
                        'orderBy'    => 'c.filterTarget',
                        'text'       => 'mautic.core.type',
                        'class'      => 'visible-md visible-lg col-focus-category',
                    ]
                );

                echo $view->render(
                    'MauticCoreBundle:Helper:tableheader.html.php',
                    [
                        'text'    => 'mautic.plugin.recommender.template',
                        'class'   => 'col-msg-name',
                        'default' => true,
                    ]
                );

                echo $view->render(
                    'MauticCoreBundle:Helper:tableheader.html.php',
                    [
                        'sessionVar' => 'recommender',
                        'orderBy'    => 'c.category',
                        'text'       => 'mautic.core.category',
                        'class'      => 'visible-md visible-lg col-focus-category',
                    ]
                );

                echo $view->render(
                    'MauticCoreBundle:Helper:tableheader.html.php',
                    [
                        'sessionVar' => 'recommender',
                        'orderBy'    => 'e.id',
                        'text'       => 'mautic.core.id',
                        'class'      => 'col-msg-id visible-md visible-lg',
                    ]
                );
                ?>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($items as $item): ?>
                <tr>
                    <td>
                        <?php
                        echo $view->render(
                            'MauticCoreBundle:Helper:list_actions.html.php',
                            [
                                'item'            => $item,
                                'templateButtons' => [
                                    'edit'   => $view['security']->hasEntityAccess(
                                        $permissions['recommender:recommender:editown'],
                                        $permissions['recommender:recommender:editother'],
                                        $item->getCreatedBy()
                                    ),
                                    'clone'  => $permissions['recommender:recommender:create'],
                                    'delete' => $view['security']->hasEntityAccess(
                                        $permissions['recommender:recommender:deleteown'],
                                        $permissions['recommender:recommender:deleteother'],
                                        $item->getCreatedBy()
                                    ),
                                ],
                                'routeBase'       => 'recommender',
                                'nameGetter'      => 'getName',
                            ]
                        );
                        ?>
                    </td>
                    <td>
                        <?php
                        /*<a href="<?php echo $view['router']->url(
                            'mautic_recommender_action',
                            ['objectAction' => 'view', 'objectId' => $item->getId()]
                        ); ?>" data-toggle="ajax">
                        </a>
                        */
                        ?>
                        <?php echo $item->getName(); ?>
                    </td>
                    <td>
                        <?php echo
                        $view['translator']->trans(
                            \MauticPlugin\MauticRecommenderBundle\Enum\FiltersEnum::getFilterTarget(
                                $item->getFilterTarget()
                            )
                        ); ?>
                    </td>
                    <td class="visible-md visible-lg">
                        <?php echo $item->getTemplate()->getName(); ?>
                    </td>
                    <td class="visible-md visible-lg">
                        <?php $category = $item->getCategory(); ?>
                        <?php $catName  = ($category) ? $category->getTitle() : $view['translator']->trans(
                            'mautic.core.form.uncategorized'
                        ); ?>
                        <?php $color = ($category) ? '#'.$category->getColor() : 'inherit'; ?>
                        <span style="white-space: nowrap;"><span class="label label-default pa-4"
                                                                 style="border: 1px solid #d5d5d5; background: <?php echo $color; ?>;"> </span> <span><?php echo $catName; ?></span></span>
                    </td>
                    <td class="visible-md visible-lg"><?php echo $item->getId(); ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        <div class="panel-footer">
            <?php echo $view->render(
                'MauticCoreBundle:Helper:pagination.html.php',
                [
                    'totalItems' => count($items),
                    'page'       => $page,
                    'limit'      => $limit,
                    'menuLinkId' => 'mautic_recommender_index',
                    'baseUrl'    => $view['router']->url('mautic_recommender_index'),
                    'sessionVar' => 'recommender',
                ]
            ); ?>
        </div>
    </div>
<?php else: ?>
    <?php echo $view->render('MauticCoreBundle:Helper:noresults.html.php'); ?>
<?php endif; ?>
