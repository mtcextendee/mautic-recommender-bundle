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
<div class="recommender-col recommender-item_{{ index }}">
    <?php if (!empty($recommender->getProperties()['itemImage'])): ?>
        <?php if (!empty($recommender->getProperties()['itemUrl'])): ?>
            <a  href="<?php echo $recommender->getProperties()['itemUrl']; ?>">
        <?php endif; ?>
        <?php if (isset($preview) && $preview) {
    ?>
            <img class="recommender-image" src="http://via.placeholder.com/350x250?text=Example" alt="">
        <?php
} else {
        ?>
            <img class="recommender-image" src="<?php echo $recommender->getProperties()['itemImage']; ?>" alt="">
        <?php
    } ?>
        <?php if (!empty($recommender->getProperties()['itemUrl'])): ?>
            </a>
        <?php endif; ?>
    <?php endif; ?>
    <?php if (!empty($recommender->getProperties()['itemName'])): ?>
        <h5 class="recommender-name"><?php echo $recommender->getProperties()['itemName']; ?></h5>
    <?php endif; ?>
    <?php if (!empty($recommender->getProperties()['itemShortDescription'])): ?>
        <p class="recombe-short-description"><?php echo $recommender->getProperties()['itemShortDescription']; ?></p>
    <?php endif; ?>
    <?php if (!empty($recommender->getProperties()['itemPrice'])): ?>
        <p class="recommender-price-case">
            <span class="recommender-price">
                <?php echo $recommender->getProperties()['itemPrice']; ?><?php if (!empty($settings['currency'])): ?><?php echo $settings['currency']; ?><?php endif; ?>
            </span>
            <?php if (!empty($recommender->getProperties()['itemOldPrice'])): ?>
                <span class="recommender-price-old"
                      style="text-decoration: line-through"><?php echo $recommender->getProperties(
                    )['itemOldPrice']; ?>€
                    <?php if (!empty($settings['currency'])): ?>
                        <?php echo $settings['currency']; ?>
                    <?php endif; ?>
                </span>
            <?php endif; ?>
        </p>
    <?php endif; ?>


    <?php if (!empty($recommender->getProperties()['itemAction'])): ?>
        <?php if (!empty($recommender->getProperties()['itemUrl'])): ?>
            <a class="recommender-action" href="<?php echo $recommender->getProperties()['itemUrl']; ?>">
        <?php endif; ?>
        <?php echo $recommender->getProperties()['itemAction']; ?>
        <?php if (!empty($recommender->getProperties()['itemUrl'])): ?>
            </a>
        <?php endif; ?>
    <?php endif; ?>

</div>
