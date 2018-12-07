<?php

/*
 * @copyright   2014 Mautic Contributors. All rights reserved
 * @author      Mautic
 *
 * @link        http://mautic.org
 *
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

$view->extend('MauticCoreBundle:FormTheme:form_simple.html.php');

?>

<?php
$view['slots']->start('primaryFormContent');
echo $view['assets']->includeStylesheet('plugins/MauticRecommenderBundle/Assets/css/recommender.css');
echo $view['assets']->includeScript('plugins/MauticRecommenderBundle/Assets/js/recommender.js');
/** @var \MauticPlugin\MauticRecommenderBundle\Entity\RecommenderTemplate $recommender */
$recommender = $entity;
?>
<div class="row">
    <div class="col-md-6">
        <?php echo $view['form']->row($form['name']); ?>
    </div>
    <div class="col-md-3">
        <?php echo $view['form']->row($form['isPublished']); ?>
    </div>
    <div class="col-md-3">
        <?php echo $view['form']->row($form['templateMode']); ?>
    </div>
</div>
<div class="row">
    <div class="col-md-12">
        <div class="row">
            <div class="col-md-12">
                <div id="recommender_properties_1"
                     data-show-on="{&quot;recommender_templates_templateMode_0&quot;:&quot;checked&quot;}">

                    <h3><?php echo $view['translator']->trans('mautic.plugin.recommender.preview'); ?></h3>
                    <hr>
                    <div id="recommender-preview-case">
                        <div style="display:none" id="recommender-preview-loader" class="lds-roller">
                            <div></div>
                            <div></div>
                            <div></div>
                            <div></div>
                            <div></div>
                            <div></div>
                            <div></div>
                            <div></div>
                        </div>
                        <div id="recommender-preview">
                            <?php

                            echo $view->render(
                                'MauticRecommenderBundle:Builder\Page:generator.html.php',
                                [
                                    'recommender' => $recommender,
                                    'settings'    => $settings,
                                    'preview'     => true,
                                ]
                            );
                            ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div id="recommender_template_1"
                     data-show-on="{&quot;recommender_templates_templateMode_1&quot;:&quot;checked&quot;}">
                    <div class="form-control-custom">
                        <?php
                        echo $view['form']->widget($form['template']['header']);
                        ?>
                        <div class="form-control-custom-disabled">{% for item in items %}</div>
                        <?php
                        echo $view['form']->widget($form['template']['body']);
                        ?>
                        <div class="form-control-custom-disabled">{% endfor %}</div>
                        <?php
                        echo $view['form']->widget($form['template']['footer']);
                        ?>
                    </div>


                </div>
            </div>
        </div>
    </div>
</div>

<br>
<?php $view['slots']->stop(); ?>

<?php $view['slots']->start('rightFormContent'); ?>


<div class="row">
    <div id="recommender_template_2"
         data-show-on="{&quot;recommender_templates_templateMode_0&quot;:&quot;checked&quot;}">

        <div class="panel-group recommender-preview" id="accordion" role="tablist" aria-multiselectable="true">
            <div class="panel panel-default">
                <div class="panel-heading" role="tab" id="headingOne">
                    <h4 class="panel-title">
                        <a role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseOne"
                           aria-expanded="false" aria-controls="collapseOne">
                            <i class="fa fa-check text-success"></i>
                            <?php echo $view['translator']->trans('mautic.plugin.recommender.container'); ?>
                        </a>
                    </h4>
                </div>
                <div id="collapseOne" class="panel-collapse collapse" role="tabpanel"
                     aria-labelledby="headingOne">
                    <div class="panel-body">
                        <?php
                        echo $view['form']->row($form['properties']['columns']);
                        echo $view['form']->row($form['numberOfItems']);
                        echo $view['form']->row($form['properties']['background']);
                        echo $view['form']->row($form['properties']['font']);
                        echo $view['form']->row($form['properties']['padding']);
                        echo $view['form']->row($form['properties']['style']);
                        ?>
                    </div>
                </div>
            </div>
            <div class="panel panel-default">
                <div class="panel-heading" role="tab" id="itemColHead">
                    <h4 class="panel-title">
                        <a class="collapsed" role="button" data-toggle="collapse" data-parent="#accordion"
                           href="#itemCol" aria-expanded="false" aria-controls="itemCol">
                            <i class="fa fa-check text-success"></i> <?php echo $view['translator']->trans(
                                'mautic.plugin.recommender.column'
                            ); ?>
                        </a>
                    </h4>
                </div>
                <div id="itemCol" class="panel-collapse collapse" role="tabpanel" aria-labelledby="itemColHead">
                    <div class="panel-body">
                        <?php
                        echo $view['form']->row($form['properties']['colPadding']);
                        echo $view['form']->row($form['properties']['colBackground']);
                        echo $view['form']->row($form['properties']['colStyle']);
                        ?>
                    </div>
                </div>
            </div>
            <div class="panel panel-default">
                <div class="panel-heading" role="tab" id="itemNameHead">
                    <h4 class="panel-title">
                        <a class="collapsed" role="button" data-toggle="collapse" data-parent="#accordion"
                           href="#itemName" aria-expanded="false" aria-controls="itemName">
                            <i class="fa fa-check <?php if (!empty($recommender->getProperties()['itemName'])
                            ): echo 'text-success'; endif; ?>"></i> <?php echo $view['translator']->trans(
                                'mautic.plugin.recommender.item.name'
                            ); ?>
                        </a>
                    </h4>
                </div>
                <div id="itemName" class="panel-collapse collapse" role="tabpanel" aria-labelledby="itemNameHead">
                    <div class="panel-body">
                        <?php
                        echo $view['form']->row($form['properties']['itemName']);
                        echo $view['form']->row($form['properties']['itemNameColor']);
                        echo $view['form']->row($form['properties']['itemNameSize']);
                        echo $view['form']->row($form['properties']['itemNamePadding']);
                        echo $view['form']->row($form['properties']['itemNameBold']);
                        echo $view['form']->row($form['properties']['itemNameStyle']);
                        ?>
                    </div>
                </div>
            </div>
            <div class="panel panel-default">
                <div class="panel-heading" role="tab" id="itemImageHead">
                    <h4 class="panel-title">
                        <a class="collapsed" role="button" data-toggle="collapse" data-parent="#accordion"
                           href="#itemImage" aria-expanded="false" aria-controls="itemImage">
                            <i class="fa fa-check <?php if (!empty($recommender->getProperties()['itemImage'])
                            ): echo 'text-success'; endif; ?>"></i> <?php echo $view['translator']->trans(
                                'mautic.plugin.recommender.item.image'
                            ); ?>
                        </a>
                    </h4>
                </div>
                <div id="itemImage" class="panel-collapse collapse" role="tabpanel" aria-labelledby="itemImageHead">
                    <div class="panel-body">
                        <?php
                        echo $view['form']->row($form['properties']['itemImage']);
                        echo $view['form']->row($form['properties']['itemImageStyle']);
                        ?>
                    </div>
                </div>
            </div>
            <div class="panel panel-default">
                <div class="panel-heading" role="tab" id="itemShortDescriptionHead">
                    <h4 class="panel-title">
                        <a class="collapsed" role="button" data-toggle="collapse" data-parent="#accordion"
                           href="#itemShortDescription" aria-expanded="false" aria-controls="itemShortDescription">
                            <i class="fa fa-check <?php if (!empty($recommender->getProperties(
                            )['itemShortDescription'])
                            ): echo 'text-success'; endif; ?>"></i> <?php echo $view['translator']->trans(
                                'mautic.plugin.recommender.item.short.description'
                            ); ?>
                        </a>
                    </h4>
                </div>
                <div id="itemShortDescription" class="panel-collapse collapse" role="tabpanel"
                     aria-labelledby="itemShortDescriptionHead">
                    <div class="panel-body">
                        <?php
                        echo $view['form']->row($form['properties']['itemShortDescription']);
                        echo $view['form']->row($form['properties']['itemShortDescriptionBold']);
                        echo $view['form']->row($form['properties']['itemShortDescriptionStyle']);
                        ?>
                    </div>
                </div>
            </div>

            <div class="panel panel-default">
                <div class="panel-heading" role="tab" id="itemPriceHead">
                    <h4 class="panel-title">
                        <a class="collapsed" role="button" data-toggle="collapse" data-parent="#accordion"
                           href="#itemPrice" aria-expanded="false" aria-controls="itemPrice">
                            <i class="fa fa-check <?php if (!empty($recommender->getProperties()['itemPrice'])
                            ): echo 'text-success'; endif; ?>"></i> <?php echo $view['translator']->trans(
                                'mautic.plugin.recommender.item.price'
                            ); ?>
                        </a>
                    </h4>
                </div>
                <div id="itemPrice" class="panel-collapse collapse" role="tabpanel" aria-labelledby="itemPriceHead">
                    <div class="panel-body">
                        <?php
                        echo $view['form']->row($form['properties']['itemPrice']);
                        echo $view['form']->row($form['properties']['itemPriceColor']);
                        echo $view['form']->row($form['properties']['itemPriceSize']);
                        echo $view['form']->row($form['properties']['itemPricePadding']);
                        echo $view['form']->row($form['properties']['itemPriceBold']);
                        echo $view['form']->row($form['properties']['itemPriceStyle']);
                        ?>
                    </div>
                </div>
            </div>
            <div class="panel panel-default">
                <div class="panel-heading" role="tab" id="itemOldPriceHead">
                    <h4 class="panel-title">
                        <a class="collapsed" role="button" data-toggle="collapse" data-parent="#accordion"
                           href="#itemOldPrice" aria-expanded="false" aria-controls="itemOldPrice">
                            <i class="fa fa-check <?php if (!empty($recommender->getProperties()['itemOldPrice'])
                            ): echo 'text-success'; endif; ?>"></i> <?php echo $view['translator']->trans(
                                'mautic.plugin.recommender.item.old.price'
                            ); ?>
                        </a>
                    </h4>
                </div>
                <div id="itemOldPrice" class="panel-collapse collapse" role="tabpanel"
                     aria-labelledby="itemOldPriceHead">
                    <div class="panel-body">
                        <?php
                        echo $view['form']->row($form['properties']['itemOldPrice']);
                        echo $view['form']->row($form['properties']['itemOldPriceColor']);
                        echo $view['form']->row($form['properties']['itemOldPriceSize']);
                        echo $view['form']->row($form['properties']['itemOldPriceBold']);
                        echo $view['form']->row($form['properties']['itemOldPriceStyle']);
                        ?>
                    </div>
                </div>
            </div>
            <div class="panel panel-default">
                <div class="panel-heading" role="tab" id="itemActionHead">
                    <h4 class="panel-title">
                        <a class="collapsed" role="button" data-toggle="collapse" data-parent="#accordion"
                           href="#itemAction" aria-expanded="false" aria-controls="itemAction">
                            <i class="fa fa-check <?php if (!empty($recommender->getProperties()['itemAction'])
                            ): echo 'text-success'; endif; ?>"></i> <?php echo $view['translator']->trans(
                                'mautic.plugin.recommender.item.action'
                            ); ?>
                        </a>
                    </h4>
                </div>
                <div id="itemAction" class="panel-collapse collapse" role="tabpanel"
                     aria-labelledby="itemActionHead">
                    <div class="panel-body">
                        <?php
                        echo $view['form']->row($form['properties']['itemUrl']);
                        ?>
                        <?php
                        echo $view['form']->row($form['properties']['itemAction']);
                        ?>
                        <?php
                        echo $view['form']->row($form['properties']['itemActionSize']);
                        ?>
                        <?php
                        echo $view['form']->row($form['properties']['itemActionBackground']);
                        ?>
                        <?php
                        echo $view['form']->row($form['properties']['itemActionHover']);
                        ?>
                        <?php
                        echo $view['form']->row($form['properties']['itemActionColor']);
                        ?>
                        <?php
                        echo $view['form']->row($form['properties']['itemActionRadius']);
                        ?>
                        <?php
                        echo $view['form']->row($form['properties']['itemActionPadding']);
                        echo $view['form']->row($form['properties']['itemActionBold']);
                        echo $view['form']->row($form['properties']['itemActionStyle']);
                        ?>
                    </div>
                </div>
            </div>

            <div class="panel panel-default">
                <div class="panel-heading" role="tab" id="headerHead">
                    <h4 class="panel-title">
                        <a class="collapsed" role="button" data-toggle="collapse" data-parent="#accordion"
                           href="#header" aria-expanded="false" aria-controls="header">
                            <i class="fa fa-check text-success"></i> <?php echo $view['translator']->trans(
                                'mautic.plugin.recommender.header'
                            ); ?>
                        </a>
                    </h4>
                </div>
                <div id="header" class="panel-collapse collapse" role="tabpanel"
                     aria-labelledby="headerHead">
                    <div class="panel-body">
                        <?php
                        echo $view['form']->row($form['properties']['header']);
                        ?>
                    </div>
                </div>
            </div>
            <div class="panel panel-default">
                <div class="panel-heading" role="tab" id="footerHead">
                    <h4 class="panel-title">
                        <a class="collapsed" role="button" data-toggle="collapse" data-parent="#accordion"
                           href="#footer" aria-expanded="false" aria-controls="footer">
                            <i class="fa fa-check text-success"></i> <?php echo $view['translator']->trans(
                                'mautic.plugin.recommender.footer'
                            ); ?>
                        </a>
                    </h4>
                </div>
                <div id="footer" class="panel-collapse collapse" role="tabpanel"
                     aria-labelledby="footerHead">
                    <div class="panel-body">
                        <?php
                        echo $view['form']->row($form['properties']['footer']);
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<div class="row" style="margin-top:-10px;">
    <div class="col-md-12">
        <div id="recommender_template"
             data-show-on="{&quot;recommender_templates_templateMode_1&quot;:&quot;checked&quot;}">

            <div class="row">
                <div class="col-xs-12">
                    <h5><?php echo $view['translator']->trans('mautic.plugin.recommender.template.tags'); ?></h5>
                    <br>
                </div>
            </div>
            <div class="row">
                <?php
                $body = '';
                if (!empty($properties)) {
                    foreach ($properties as $property) {
                        $body .= '<div class="col-sm-6">';
                        $body .= '{{ '.$property['name'].' }}';
                        $body .= '</div>';
                    }
                }
                echo $body;
                ?>
            </div>
        </div>
    </div>
</div>

<div class="ide">
    <?php echo $view['form']->rest($form); ?>
</div>


<?php $view['slots']->stop(); ?>

