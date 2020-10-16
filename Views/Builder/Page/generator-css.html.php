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

?>

<style>
    .<?php echo $class; ?> .recommender-row {
        display: grid;
        grid-template-columns: repeat(12, 1fr);
    <?php if (!empty($recommender->getProperties()['background'])):    echo 'background-color:#'.$recommender->getProperties()['background']; endif; ?>;
    <?php if (!empty($recommender->getProperties()['font'])):    echo 'font-family:'.$recommender->getProperties()['font']; endif; ?>;
    <?php if (!empty($recommender->getProperties()['padding'])):    echo 'padding:'.$recommender->getProperties()['padding']; endif; ?>;
    <?php if (!empty($recommender->getProperties()['style'])):    echo $recommender->getProperties()['style']; endif; ?>;

    }

    .<?php echo $class; ?> .recommender-col {
        grid-column: span <?php echo    isset($recommender->getProperties()['columns']) ? $recommender->getProperties()['columns'] : 4; ?>;
    <?php if (!empty($recommender->getProperties()['colBackground'])):    echo 'background-color:#'.$recommender->getProperties()['colBackground']; endif; ?>;
    <?php if (!empty($recommender->getProperties()['colPadding'])):    echo 'padding:'.$recommender->getProperties()['colPadding']; endif; ?>;
    <?php if (!empty($recommender->getProperties()['colStyle'])):    echo $recommender->getProperties()['colStyle']; endif; ?>;

    }

    .<?php echo $class; ?> .recommender-image {
        display: block;
        max-width: 100%;
    <?php if (!empty($recommender->getProperties()['itemImageStyle'])):    echo $recommender->getProperties()['itemImageStyle']; endif; ?>;
    <?php if (!empty($recommender->getProperties()['columns']) && 12 == $recommender->getProperties()['columns']):    echo 'margin-right: 20px;
    float: left;
}'; endif; ?>;
    }

    .<?php echo $class; ?> .recombe-short-description {
    <?php if (!empty($recommender->getProperties()['itemShortDescriptionStyle'])):    echo $recommender->getProperties()['itemShortDescriptionStyle']; endif; ?>;
    }

    .<?php echo $class; ?> .recommender-action {
    <?php if (!empty($recommender->getProperties()['itemActionBackground'])):    echo 'background-color:#'.$recommender->getProperties()['itemActionBackground']; endif; ?>;
    <?php if (!empty($recommender->getProperties()['itemActionColor'])):    echo 'color:#'.$recommender->getProperties()['itemActionColor']; endif; ?>;
    <?php if (!empty($recommender->getProperties()['itemActionRadius'])):    echo 'border-radius:'.$recommender->getProperties()['itemActionRadius']; endif; ?>;
    <?php if (!empty($recommender->getProperties()['itemActionPadding'])):    echo 'padding:'.$recommender->getProperties()['itemActionPadding']; endif; ?>;
    <?php if (!empty($recommender->getProperties()['itemActionSize'])):    echo 'font-size:'.$recommender->getProperties()['itemActionSize']; endif; ?>;
    <?php if (!empty($recommender->getProperties()['itemActionStyle'])):    echo $recommender->getProperties()['itemActionStyle']; endif; ?>;

    }

    .<?php echo $class; ?> .recommender-action:hover {
    <?php if (!empty($recommender->getProperties()['itemActionHover'])):    echo 'background-color:#'.$recommender->getProperties()['itemActionHover']; endif; ?>;
    }

    .<?php echo $class; ?> .recommender-name {
    <?php if (!empty($recommender->getProperties()['itemNameColor'])):    echo 'color:#'.$recommender->getProperties()['itemNameColor']; endif; ?>;
    <?php if (!empty($recommender->getProperties()['itemNameSize'])):    echo 'font-size:'.$recommender->getProperties()['itemNameSize']; endif; ?>;
    <?php if (!empty($recommender->getProperties()['itemNamePadding'])):    echo 'padding:'.$recommender->getProperties()['itemNamePadding']; endif; ?>;
    <?php if (!empty($recommender->getProperties()['itemNameStyle'])):    echo $recommender->getProperties()['itemNameStyle']; endif; ?>;
    <?php if (!empty($recommender->getProperties()['itemNameBold'])):    echo 'font-weight:bold'; else: echo 'font-weight:normal'; endif; ?>;
    }

    .<?php echo $class; ?> .recommender-price-case {
    <?php if (!empty($recommender->getProperties()['itemPricePadding'])):    echo 'padding:'.$recommender->getProperties()['itemPricePadding']; endif; ?>;
    }

    .<?php echo $class; ?> .recommender-price {
    <?php if (!empty($recommender->getProperties()['itemPriceColor'])):    echo 'color:#'.$recommender->getProperties()['itemPriceColor']; endif; ?>;
    <?php if (!empty($recommender->getProperties()['itemPriceSize'])):    echo 'font-size:'.$recommender->getProperties()['itemPriceSize']; endif; ?>;
    <?php if (!empty($recommender->getProperties()['itemPriceBold'])):    echo 'font-weight:bold'; else: echo 'font-weight:normal'; endif; ?>;
    <?php if (!empty($recommender->getProperties()['itemPriceStyle'])):    echo $recommender->getProperties()['itemPriceStyle']; endif; ?>;

    }

    .<?php echo $class; ?> .recommender-price-old {
    <?php if (!empty($recommender->getProperties()['itemOldPriceColor'])):    echo 'color:#'.$recommender->getProperties()['itemOldPriceColor']; endif; ?>;
    <?php if (!empty($recommender->getProperties()['itemOldPriceSize'])):    echo 'font-size:'.$recommender->getProperties()['itemOldPriceSize']; endif; ?>;
    <?php if (!empty($recommender->getProperties()['itemOldPriceStyle'])):    echo $recommender->getProperties()['itemOldPriceStyle']; endif; ?>;
    }

</style>