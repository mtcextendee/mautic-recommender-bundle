<?php

/*
 * @copyright   2014 Mautic Contributors. All rights reserved
 * @author      Mautic
 *
 * @link        http://mautic.org
 *
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

if (!isset($preview)) {
    $preview = false;
}
if (!isset($settings)) {
    $settings = [];
}
?>
<?php
if (isset($recommender->getProperties()['footer'])) {
    if ($preview) {
        echo html_entity_decode($recommender->getProperties()['footer']); ?>
<?php
    } else {
        echo $recommender->getProperties()['footer']; ?>
<?php
    }
}
?>
    </div>
</div>
