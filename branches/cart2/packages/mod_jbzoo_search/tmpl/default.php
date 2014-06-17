<?php
/**
 * JBZoo App is universal Joomla CCK, application for YooTheme Zoo component
 *
 * @package     jbzoo
 * @version     2.x Pro
 * @author      JBZoo App http://jbzoo.com
 * @copyright   Copyright (C) JBZoo.com,  All rights reserved.
 * @license     http://jbzoo.com/license-pro.php JBZoo Licence
 * @coder       Denis Smetannikov <denis@jbzoo.com>
 */

// no direct access
defined('_JEXEC') or die('Restricted access');


$zoo = App::getInstance('zoo');

// init assets
$zoo->jbassets->filter($itemLayout);

$formId = 'jbzoo-filter-' . $itemLayout . '-' . $module->id;
?>

<?php if ((int)$params->get('autosubmit', 0)) : ?>
    <script type="text/javascript">
        jQuery(function ($) {
            $('#<?php echo $formId;?> select, #<?php echo $formId;?> input[type=radio], #<?php echo $formId;?> input[type=checkbox]').change(function () {
                $(this).closest("form").submit();
            });
        });
    </script>
<?php endif; ?>

<div class="jbzoo jbzoo-filter-wrapper">

    <form class="jbzoo-filter filter-<?php echo $itemLayout; ?>"
          id="<?php echo $formId; ?>"
          method="get"
          action="<?php echo JRoute::_('index.php?Itemid=' . $params->get('menuitem', JRequest::getVar('Itemid'))); ?>"
          name="<?php echo $formId; ?>">

        <div class="filter-fields">
            <!--
                User fields
            -->
            <?php
            echo $renderer->render('item.' . $itemLayout, array(
                    'params'      => $params,
                    'type'        => $type,
                    'layout'      => $itemLayout,
                    'application' => $application,
                ));
            ?>

            <!--
                Static fields
            -->
            <div class="static-fields">
                <?php if ((int)$params->get('pages_show', 1)) : ?>
                    <div class="jbzoofilter_row element pages">
                        <label for="filterEl_limit" class="label"><?php echo JText::_('JBZOO_PAGES'); ?></label>

                        <div class="field"><?php echo $pagesHTML; ?></div>
                        <div class="clear"></div>
                    </div>
                <?php else : ?><?php echo $pagesHTML; ?><?php endif; ?>

                <?php if ((int)$params->get('order_show', 1) && !empty($orderList)) : ?>
                    <div class="jbzoofilter_row element ordering">
                        <label for="filterEl_orderings" class="label"><?php echo JText::_('JBZOO_ORDER'); ?></label>

                        <div class="field"><?php echo $orderingsHTML; ?></div>
                        <div class="clear"></div>
                    </div>
                <?php else : ?>
                    <?php echo $orderingsHTML; ?>
                <?php endif; ?>


                <?php if ((int)$params->get('logic_show', 1)) : ?>
                    <div class="jbzoofilter_row element logic">
                        <label for="filterEl_logic" class="label"><?php echo JText::_('JBZOO_LOGIC'); ?></label>

                        <div class="field"><?php echo $logicHTML; ?></div>
                        <div class="clear"></div>
                    </div>
                <?php else : ?><?php echo $logicHTML; ?><?php endif; ?>
            </div>
        </div>

        <!--
            Submit and reset buttons
        -->
        <div class="controls">
            <?php if ((int)$params->get('button_submit_show', 1)) : ?>
                <input type="submit" name="send-form" value="<?php echo JText::_('JBZOO_BUTTON_SUBMIT'); ?>"
                       class="jsSubmit button rborder"/>
            <?php endif; ?>

            <?php if ((int)$params->get('button_reset_show', 0)) : ?>
                <input type="button" name="reset-form" value="<?php echo JText::_('JBZOO_BUTTON_RESET'); ?>"
                       class="reset button rborder jsFormReset"/>
                <script type="text/javascript">
                    jQuery(function ($) {
                        $('#<?php echo $formId;?> .jsFormReset').unbind().click(function () {
                            $('#<?php echo $formId;?> .filter-element, #<?php echo $formId;?> .static-fields').each(function (n, obj) {

                                var $obj = $(obj),
                                    $input = $obj.find(':input').not(':button, :submit, :reset, input[type="hidden"]');

                                $input.val('').trigger("liszt:updated");

                                $input.val('')
                                    .removeAttr('checked')
                                    .removeAttr('selected')

                                if ($input.is('select') && $input.attr('multiple') != 'multiple') {
                                    $('option:eq(0)', $input).attr('selected', 'selected');
                                }

                                if ($obj.hasClass('element-jbpriceadvance') || $obj.hasClass('element-slider')) {
                                    var slider = $obj.find('.ui-slider').data('slider');
                                    slider.values([slider.options.min, slider.options.max]);
                                    $('.slider-value-0', $obj).html(slider.options.min);
                                    $('.slider-value-1', $obj).html(slider.options.max);
                                    $('[type=hidden][name*="range"]', $obj).val(slider.options.min + '/' + slider.options.max);
                                }

                                if ($obj.find('.radio-lbl').length > 0) {
                                    $('input[type=radio]:eq(0)', $obj).trigger('click');
                                }

                                $input.trigger('change');
                            });

                            return false;
                        });
                    });
                </script>
            <?php endif; ?>

            <div class="clear clr"></div>
        </div>

        <!--
            System required fields
        -->
        <input type="hidden" name="controller" value="search"/>
        <input type="hidden" name="Itemid" value="<?php echo $params->get('menuitem', JRequest::getVar('Itemid')); ?>"/>
        <input type="hidden" name="option" value="com_zoo"/>
        <input type="hidden" name="task" value="filter"/>
        <input type="hidden" name="exact" value="<?php echo $params->get('exact', 0); ?>"/>
        <input type="hidden" name="type" value="<?php echo $type; ?>" class="jsItemType"/>
        <input type="hidden" name="app_id" value="<?php echo $application->id; ?>" class="jsApplicationId"/>
    </form>

</div>