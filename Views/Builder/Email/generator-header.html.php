<?php

/*
 * @copyright   2014 Mautic Contributors. All rights reserved
 * @author      Mautic
 *
 * @link        http://mautic.org
 *
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

$class = 'recommender-template-'.$recommender->getId();
if (!isset($preview)) {
    $preview = false;
}
if (!isset($settings)) {
    $settings = [];
}
?>
<center>
<table width="100%" border="0" cellpadding="0" cellspacing="0" bgcolor="#F2F2F2" class="recommender-global-row <?php echo $class; ?>">
    <tr>
        <td align="center" valign="top">
            <?php if ($preview) {
    echo html_entity_decode($recommender->getProperties()['header']); ?>
            <?php
} else {
        echo $recommender->getProperties()['header']; ?>
            <?php
    } ?>
        </td>
    </tr>
    <tr>
        <td align="center" valign="top">
            <table width="100%" border="0" cellpadding="0" cellspacing="0" bgcolor="#F2F2F2" class="recommender-global-row <?php echo $class; ?>">
