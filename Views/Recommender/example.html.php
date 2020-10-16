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
<?php if ('index' == $tmpl): ?>
    <h3 class="col-xs-12"><?php echo $view['translator']->trans(
            'mautic.plugin.recommender.form.testing_area'
        ); ?>
    </h3>
<div class="contact-form">
    <div class="col-xs-6">
        <?php echo $view->render(
            'MauticCoreBundle:Helper:search.html.php',
            [
                'searchValue' => $searchValue,
                'action'      => $action,
                'searchHelp'  => false,
                'target'      => '.contact-options',
                'tmpl'        => 'update',
            ]
        ); ?></div>
    <div class="contact-options mt-5">
        <?php endif; ?>

        <div class="col-xs-4"><?php echo $view['form']->form($form); ?></div>
        <div class="col-xs-12">        
            <div class="collapse" id="recommender-sql-query">
                <textarea style="width:100%" rows="5" readonly>
                    <?php foreach ($sqlQuery as $query) {
            echo $query.'
                        ';
        } ?>
                </textarea>
            </div>
            <!-- lead detail collapseable toggler -->
            <div class="hr-expand nm">
                <span data-toggle="tooltip" title="<?php echo $view['translator']->trans('mautic.plugin.recommender.form.example.sqlquery.title'); ?>">
                    <a href="javascript:void(0)" class="arrow text-muted collapsed" data-toggle="collapse" data-target="#recommender-sql-query">
                       <span class="caret"></span>
                       <?php echo $view['translator']->trans('mautic.plugin.recommender.form.example.sqlquery'); ?>
                    </a>
                </span>
            </div>
            <!--/ lead detail collapseable toggler -->

            <br>
            <br>
            <?php if ($contactId): ?>
                <h3><?php echo $view['translator']->trans(
                        'mautic.plugin.recommender.form.example.content.filter.contact',
                        ['%contactId%' => $contactId]
                    ); ?></h3>
            <?php else: ?>
                <h3><?php echo $view['translator']->trans(
                        'mautic.plugin.recommender.form.example.content.filter'
                    ); ?></h3>
            <?php endif; ?>
            <hr>
            <?php echo $cnt; ?></div>
        <?php if ('index' == $tmpl): ?>
    </div>
</div>
<?php endif; ?>

