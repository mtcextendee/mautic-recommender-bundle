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

<div class="row">
    <div class="col-xs-8">
        <?php echo $view['form']->row($form['notification']); ?>
    </div>
    <div class="col-xs-12 mb-20">
            <?php echo $view['form']->row($form['newNotificationButton']); ?>
            <?php echo $view['form']->row($form['editNotificationButton']); ?>
    </div>
</div>
<div class="row">
    <div class="col-xs-8">
        <?php echo $view['form']->row($form['type']); ?>
    </div>
</div>