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
<?php
echo $view->render(
    'MauticRecommenderBundle:Builder\Page:generator-css.html.php',
    [
        'recommender' => $recommender,
        'settings'    => $settings,
        'preview'     => $preview,
    ]
);
?>

<div class="recommender-global-row <?php echo $class; ?>">

    <?php
    if (isset($recommender->getProperties()['header'])) {
        if ($preview) {
            echo html_entity_decode($recommender->getProperties()['header']); ?>
    <?php
        } else {
            echo $recommender->getProperties()['header']; ?>
    <?php
        }
    }
    ?>
    <div class="recommender-row">
