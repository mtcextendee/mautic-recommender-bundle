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
<?php if ($tmpl == 'index'): ?>
<hr>
    <h3 class="col-xs-12"><?php echo $view['translator']->trans(
            'mautic.plugin.recommender.form.testing_area'
        ); ?>
    </h3>
<br style="clear:both">
<div class="contact-form">
    <div class="col-xs-4">
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
            <h4>SQL query:</h4>
            <hr>
            <textarea style="width:100%" rows="5" readonly><?php echo $sqlQuery; ?></textarea>
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
        <?php if ($tmpl == 'index'): ?>
    </div>
</div>
<?php endif; ?>

