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
/** @var \MauticPlugin\MauticRecommenderBundle\Entity\Recommender $recommender */
$recommender = $entity;

$fields = $form->vars['fields'];
$index  = count($form['filters']->vars['value']) ? max(array_keys($form['filters']->vars['value'])) : 0;
$id     = $form->vars['data']->getId();
?>
<div class="row">
    <div class="col-md-4">
        <?php echo $view['form']->row($form['name']); ?>
    </div>
    <div class="col-md-4">
        <?php echo $view['form']->row($form['template']); ?>
    </div>
    <div class="col-md-4">
        <?php echo $view['form']->row($form['filter']); ?>
    </div>
    <div class="col-md-12">

        <div class="form-group" >
            <div id="<?php echo $form->vars['id'] ?>"  class="dwc-filter available-filters mb-md pl-0 col-md-4" data-prototype="<?php echo $view->escape($view['form']->widget($form['filters']->vars['prototype'])); ?>" data-index="<?php echo $index + 1; ?>">
                <select class="chosen form-control" id="available_filters">
                    <option value=""></option>
                    <?php
                    foreach ($fields as $object => $field):
                        $header = $object;
                        $icon   = ($object == 'company') ? 'building' : 'user';
                        ?>
                        <optgroup label="<?php echo $view['translator']->trans('mautic.lead.'.$header); ?>">
                            <?php foreach ($field as $value => $params):
                                $list      = (!empty($params['properties']['list'])) ? $params['properties']['list'] : [];
                                $choices   = \Mautic\LeadBundle\Helper\FormFieldHelper::parseList($list, true, ('boolean' === $params['properties']['type']));
                                $list      = json_encode($choices);
                                $callback  = (!empty($params['properties']['callback'])) ? $params['properties']['callback'] : '';
                                $operators = (!empty($params['operators'])) ? $view->escape(json_encode($params['operators'])) : '{}';
                                ?>
                                <option value="<?php echo $view->escape($value); ?>"
                                        id="available_<?php echo $object.'_'.$value; ?>"
                                        data-field-object="<?php echo $object; ?>"
                                        data-field-type="<?php echo $params['properties']['type']; ?>"
                                        data-field-list="<?php echo $view->escape($list); ?>"
                                        data-field-callback="<?php echo $callback; ?>"
                                        data-field-operators="<?php echo $operators; ?>"
                                        class="segment-filter <?php echo $icon; ?>">
                                    <?php echo $view['translator']->trans($params['label']); ?>
                                </option>
                            <?php endforeach; ?>
                        </optgroup>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="clearfix"></div>
        </div>
        <div class="selected-filters" id="recommender_filters">
            <?php if (!empty($filterErrors)): ?>
                <div class="alert alert-danger has-error">
                    <?php echo $view['form']->errors($form['filters']); ?>
                </div>
            <?php endif ?>
            <?php echo $view['form']->widget($form['filters']); ?>
        </div>
    </div>
</div>

<div class="ide">
    <?php echo $view['form']->rest($form); ?>
</div>




<?php $view['slots']->stop(); ?>

