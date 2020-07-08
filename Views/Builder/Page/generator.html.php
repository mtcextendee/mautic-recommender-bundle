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

echo $view->render(
    'MauticRecommenderBundle:Builder\Page:generator-header.html.php',
    [
        'recommender' => $recommender,
        'settings'    => $settings,
        'preview'     => $preview,
    ]
);
?>
<?php for ($i = 0; $i < $recommender->getNumberOfItems(); ++$i): ?>
    <?php
    echo $view->render(
        'MauticRecommenderBundle:Builder\Page:generator-body.html.php',
        [
            'recommender' => $recommender,
            'settings'    => $settings,
            'preview'     => $preview,
        ]
    );
    ?>
<?php endfor; ?>
<?php
echo $view->render(
    'MauticRecommenderBundle:Builder\Page:generator-footer.html.php',
    [
        'recommender' => $recommender,
        'settings'    => $settings,
        'preview'     => $preview,
    ]
);
?>