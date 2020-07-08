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
echo $view['assets']->includeScript('plugins/MauticRecommenderBundle/Assets/js/recommender.js');

/** @var \MauticPlugin\MauticRecommenderBundle\Entity\Recommender $recommender */
$recommender = $entity;

$fields       = $form->vars['fields'];
$index        = count($form['filters']->vars['value']) ? max(array_keys($form['filters']->vars['value'])) : 0;
$id           = $form->vars['data']->getId();
$filterErrors = ($view['form']->containsErrors($form['filters'])) ? 'class="text-danger"' : '';

$templates = [
    'select' => 'select-template',
];

?>
<div class="row ">
    <div class="col-md-9  height-auto">
        <div class="row">
            <div class="col-md-8">
                <?php echo $view['form']->row($form['name']); ?>
            </div>
            <div class="col-md-4">
                <?php echo $view['form']->row($form['numberOfItems']); ?>
            </div>
            <div class="col-md-12">
                <div class="row">
                    <div class="col-md-4"><?php echo $view['form']->row($form['template']); ?></div>
                    <br>
                        <?php echo $view['form']->row($form['newRecommenderButton']); ?>
                        <?php echo $view['form']->row($form['editRecommenderButton']); ?>
                </div>
            </div>

            <div class="col-md-3">
                <?php echo $view['form']->row($form['filter']); ?>
            </div>
            <div class="row">
                <div data-show-on="{&quot;recommender_filter&quot;:&quot;filters&quot;}"
                     id="<?php echo $form->vars['id'] ?>" class="col-xs-12 dwc-filter available-filters"
                     data-prototype="<?php echo $view->escape(
                         $view['form']->widget($form['filters']->vars['prototype'])
                     ); ?>" data-index="<?php echo $index + 1; ?>">
                    <div class="col-md-3">
                        <select class="chosen form-control" id="available_filters">
                            <option value=""></option>
                            <?php
                            foreach ($fields as $object => $field):
                                $header = $object;
                                $icon   = ($object == 'company') ? 'building' : 'user';
                                ?>
                                <optgroup label="<?php echo $view['translator']->trans('mautic.lead.'.$header); ?>">
                                    <?php foreach ($field as $value => $params):
                                        $list    = (!empty($params['properties']['list'])) ? $params['properties']['list'] : [];
                                        $choices = \Mautic\LeadBundle\Helper\FormFieldHelper::parseList(
                                            $list,
                                            true,
                                            ('boolean' === $params['properties']['type'])
                                        );
                                        $list      = json_encode($choices);
                                        $callback  = (!empty($params['properties']['callback'])) ? $params['properties']['callback'] : '';
                                        $operators = (!empty($params['operators'])) ? $view->escape(
                                            json_encode($params['operators'])
                                        ) : '{}';
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
                    <div class="col-md-9 selected-filters"
                         id="recommender_filters">
                         <div class="row">
                            <?php echo $view['form']->row($form['filterTarget']); ?>
                            <?php echo $view['form']->widget($form['filters']); ?>                            
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3  height-auto bdr-l">
        <h4><strong><?php echo $view['translator']->trans('mautic.plugin.recommender.form.order_by'); ?></strong></h4>
        <br>
        <?php echo $view['form']->row($form['tableOrder']->children['column']); ?>
        <?php echo $view['form']->row($form['tableOrder']->children['direction']); ?>
        <div class="order-function-choice">
            <?php echo $view['form']->row($form['tableOrder']->children['function']); ?>
        </div>
    </div>
</div>


<div class="hide" id="templates">
    <?php foreach ($templates as $dataKey => $template): ?>
        <?php $attr = ($dataKey == 'tags') ? ' data-placeholder="'.$view['translator']->trans(
                'mautic.lead.tags.select_or_create'
            )
            .'" data-no-results-text="'.$view['translator']->trans('mautic.lead.tags.enter_to_create')
            .'" data-allow-add="true" onchange="Mautic.createLeadTag(this)"' : ''; ?>
        <select class="form-control not-chosen <?php echo $template; ?>"
                name="recommender[filters][__name__][filter]"
                id="recommender_filters___name___filter"<?php echo $attr; ?>>
            <?php
            if (isset($form->vars[$dataKey])):
                foreach ($form->vars[$dataKey] as $value => $label):
                    if (is_array($label)):
                        echo "<optgroup label=\"$value\">\n";
                        foreach ($label as $optionValue => $optionLabel):
                            echo "<option value=\"$optionValue\">$optionLabel</option>\n";
                        endforeach;
                        echo "</optgroup>\n";
                    else:
                        if ($dataKey == 'lists' && (isset($currentListId) && (int) $value === (int) $currentListId)) {
                            continue;
                        }
                        echo "<option value=\"$value\">$label</option>\n";
                    endif;
                endforeach;
            endif;
            ?>
        </select>
    <?php endforeach; ?>
</div>


<?php $view['slots']->stop(); ?>

<?php $view['slots']->start('postFormContent'); ?>
<?php if (isset($tester)): ?>
    <?php echo $tester; ?>
<?php endif; ?>
<?php $view['slots']->stop(); ?>


