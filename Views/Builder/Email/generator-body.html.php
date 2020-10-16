<?php

/*
 * @copyright   2014 Mautic Contributors. All rights reserved
 * @author      Mautic
 *
 * @link        http://mautic.org
 *
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

/**
 * Classes and styles are usually not allowed in email services. (eg: gmail strips classes)
 * Due to this, css rules must be applied to tags directly in style attributes.
 */
$style = [];

$style['recommender-row'] = [];
if (!empty($recommender->getProperties()['background'])):
    $style['recommender-row'][] = 'background-color:#'.$recommender->getProperties()['background'];
endif;
if (!empty($recommender->getProperties()['font'])):
    $style['recommender-row'][] = 'font-family:'.$recommender->getProperties()['font'];
endif;
if (!empty($recommender->getProperties()['padding'])):
    $style['recommender-row'][] = 'padding:'.$recommender->getProperties()['padding'];
endif;
if (!empty($recommender->getProperties()['style'])):
    $style['recommender-row'][] = $recommender->getProperties()['style'];
endif;
$style['recommender-row'][] = '';
$style['recommender-row']   = implode('; ', $style['recommender-row']);

$style['recommender-col'] = ['vertical-align: top'];
if (!empty($recommender->getProperties()['colBackground'])):
    $style['recommender-col'][] = 'background-color:#'.$recommender->getProperties()['colBackground'];
endif;
if (!empty($recommender->getProperties()['colPadding'])):
    $style['recommender-col'][] = 'padding:'.$recommender->getProperties()['colPadding'];
endif;
if (!empty($recommender->getProperties()['colStyle'])):
    $style['recommender-col'][] = $recommender->getProperties()['colStyle'];
endif;
$style['recommender-col'][] = '';
$style['recommender-col']   = implode('; ', $style['recommender-col']);

$style['recommender-image'] = ['display: block', 'max-width: 100%'];
if (!empty($recommender->getProperties()['itemImageStyle'])):
    $style['recommender-image'][] = $recommender->getProperties()['itemImageStyle'];
endif;
if (!empty($recommender->getProperties()['columns']) && 12 == $recommender->getProperties()['columns']):
    $style['recommender-image'][] = 'margin-right: 20px';
    $style['recommender-image'][] = 'float: left';
endif;
$style['recommender-image'][] = '';
$style['recommender-image']   = implode('; ', $style['recommender-image']);

$style['recombe-short-description'] = [];
if (!empty($recommender->getProperties()['itemShortDescriptionStyle'])):
    $style['recombe-short-description'][] = $recommender->getProperties()['itemShortDescriptionStyle'];
endif;
$style['recombe-short-description'][] = '';
$style['recombe-short-description']   = implode('; ', $style['recombe-short-description']);

$style['recommender-action'] = [];
if (!empty($recommender->getProperties()['itemActionBackground'])):
    $style['recommender-action'][] =  'background-color:#'.$recommender->getProperties()['itemActionBackground'];
endif;
if (!empty($recommender->getProperties()['itemActionColor'])):
    $style['recommender-action'][] =  'color:#'.$recommender->getProperties()['itemActionColor'];
endif;
if (!empty($recommender->getProperties()['itemActionRadius'])):
    $style['recommender-action'][] =  'border-radius:'.$recommender->getProperties()['itemActionRadius'];
endif;
if (!empty($recommender->getProperties()['itemActionPadding'])):
    $style['recommender-action'][] =  'padding:'.$recommender->getProperties()['itemActionPadding'];
endif;
if (!empty($recommender->getProperties()['itemActionSize'])):
    $style['recommender-action'][] =  'font-size:'.$recommender->getProperties()['itemActionSize'];
endif;
if (!empty($recommender->getProperties()['itemActionStyle'])):
    $style['recommender-action'][] =  $recommender->getProperties()['itemActionStyle'];
endif;
$style['recommender-action'][] = '';
$style['recommender-action']   = implode('; ', $style['recommender-action']);

$style['recommender-name'] = ['margin: 0'];
if (!empty($recommender->getProperties()['itemNameColor'])):
    $style['recommender-name'][] =  'color:#'.$recommender->getProperties()['itemNameColor'];
endif;
if (!empty($recommender->getProperties()['itemNameSize'])):
    $style['recommender-name'][] =  'font-size:'.$recommender->getProperties()['itemNameSize'];
endif;
if (!empty($recommender->getProperties()['itemNamePadding'])):
    $style['recommender-name'][] =  'padding:'.$recommender->getProperties()['itemNamePadding'];
endif;
if (!empty($recommender->getProperties()['itemNameStyle'])):
    $style['recommender-name'][] =  $recommender->getProperties()['itemNameStyle'];
endif;
$style['recommender-name'][] = 'font-weight:'.!empty($recommender->getProperties()['itemNameBold']) ? 'bold' : 'normal';
$style['recommender-name'][] = '';
$style['recommender-name']   = implode('; ', $style['recommender-name']);

$style['recommender-price-case'] = [];
if (!empty($recommender->getProperties()['itemPricePadding'])):
    $style['recommender-price-case'][] =  'padding:'.$recommender->getProperties()['itemPricePadding'];
endif;
$style['recommender-price-case'][] = '';
$style['recommender-price-case']   = implode('; ', $style['recommender-price-case']);

$style['recommender-price'] = [];
if (!empty($recommender->getProperties()['itemPriceColor'])):
    $style['recommender-price'][] =  'color:#'.$recommender->getProperties()['itemPriceColor'];
endif;
if (!empty($recommender->getProperties()['itemPriceSize'])):
    $style['recommender-price'][] =  'font-size:'.$recommender->getProperties()['itemPriceSize'];
endif;

$style['recommender-price'][] = 'font-weight:'.!empty($recommender->getProperties()['itemPriceBold']) ? 'bold' : 'normal';

if (!empty($recommender->getProperties()['itemPriceStyle'])):
    $style['recommender-price'][] =  $recommender->getProperties()['itemPriceStyle'];
endif;
$style['recommender-price'][] = '';
$style['recommender-price']   = implode('; ', $style['recommender-price']);

$style['recommender-price-old'] = [];
if (!empty($recommender->getProperties()['itemOldPriceColor'])):
    $style['recommender-price-old'][] =  'color:#'.$recommender->getProperties()['itemOldPriceColor'];
endif;
if (!empty($recommender->getProperties()['itemOldPriceSize'])):
    $style['recommender-price-old'][] =  'font-size:'.$recommender->getProperties()['itemOldPriceSize'];
endif;
if (!empty($recommender->getProperties()['itemOldPriceStyle'])):
    $style['recommender-price-old'][] =  $recommender->getProperties()['itemOldPriceStyle'];
endif;
$style['recommender-price-old'][] = '';
$style['recommender-price-old']   = implode('; ', $style['recommender-price-old']);

if (isset($index)) {
    $i = $index;
    if (0 == $i || 0 === ($i % $recommender->getProperties()['columns'])) {
        if ($i > 0) {
            ?>
            </tr>
            <?php
        } ?>
        <tr style="<?php echo $style['recommender-row']; ?>">
        <?php
    }
} else {
    echo '{% if index == 0 or index is divisible by('. 12 / $recommender->getProperties()['columns'].') %}';
    echo '{% if index > 0 %}';
    echo '</tr>';
    echo '{% endif %}';
    echo '<tr style="'.$style['recommender-row'].'">';
    echo '{% endif %}';
}

?>

    <td style="<?php echo $style['recommender-col']; ?>" >
        <?php
        if (!empty($recommender->getProperties()['itemImage'])): ?>
            <?php if (!empty($recommender->getProperties()['itemUrl'])): ?>
                <a  href="<?php echo $recommender->getProperties()['itemUrl']; ?>">
            <?php endif; ?>
            <?php if (isset($preview) && $preview) {
            ?>
                <img class="recommender-image" 
                     src="http://via.placeholder.com/350x250?text=Example" 
                     alt="<?php if (!empty($recommender->getProperties()['itemName'])):    echo 'color:#'.$recommender->getProperties()['itemName'];
            endif; ?>" 
                     style="<?php echo $style['recommender-image']; ?>">
            <?php
        } else {
            ?>
                <img class="recommender-image" 
                     src="<?php echo $recommender->getProperties()['itemImage']; ?>" 
                     alt="<?php if (!empty($recommender->getProperties()['itemName'])):    echo 'color:#'.$recommender->getProperties()['itemName'];
            endif; ?>" 
                     style="<?php echo $style['recommender-image']; ?>">
            <?php
        } ?>
            <?php if (!empty($recommender->getProperties()['itemUrl'])): ?>
                </a>
            <?php endif; ?>
        <?php endif; ?>
        <?php if (!empty($recommender->getProperties()['itemName'])): ?>
            <h5 class="recommender-name" style="<?php echo $style['recommender-name']; ?>"><?php echo $recommender->getProperties()['itemName']; ?></h5>
        <?php endif; ?>
        <?php if (!empty($recommender->getProperties()['itemShortDescription'])): ?>
            <p class="recombe-short-description" style="<?php echo $style['recombe-short-description']; ?>"><?php echo $recommender->getProperties()['itemShortDescription']; ?></p>
        <?php endif; ?>
        <?php if (!empty($recommender->getProperties()['itemPrice'])): ?>
            <p class="recommender-price-case" style="<?php echo $style['recommender-price-case']; ?>">
            <span class="recommender-price" style="<?php echo $style['recommender-price']; ?>">
                <?php echo $recommender->getProperties(
                )['itemPrice']; ?><?php if (!empty($settings['currency'])): ?><?php echo $settings['currency']; ?><?php endif; ?>
            </span>
                <?php if (!empty($recommender->getProperties()['itemOldPrice'])): ?>
                    <span class="recommender-price-old" style="<?php echo $style['recommender-price-old']; ?>"
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
                <a class="recommender-action" style="<?php echo $style['recommender-action']; ?>" href="<?php echo $recommender->getProperties()['itemUrl']; ?>">
            <?php endif; ?>
            <?php echo $recommender->getProperties()['itemAction']; ?>
            <?php if (!empty($recommender->getProperties()['itemUrl'])): ?>
                </a>
            <?php endif; ?>
        <?php endif; ?>
    </td>
<?php
