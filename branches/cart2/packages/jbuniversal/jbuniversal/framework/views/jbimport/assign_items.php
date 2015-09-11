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

$actionUrl = $this->app->jbrouter->admin(array('task' => 'itemsSteps'));
$jbform = $this->app->jbform;
?>

<div class="uk-grid">
    <div id="sidebar" class="uk-width-1-6">
        <?php echo $this->partial('navigation'); ?>
    </div>

    <div class="uk-width-4-6">

        <h2><?php echo JText::_('JBZOO_ADMIN_TITLE_IMPORT_ITEMS_FIELDS'); ?></h2>

        <form class="jbzoo-import-fields jbadminform uk-form uk-form-horizontal" id="jbzooimport"
              action="<?php echo $actionUrl; ?>" name="jbzooimport" method="post" enctype="multipart/form-data">

            <fieldset class="items">

                <div class="assign-group">
                    <div class="uk-form-row">
                        <input type="submit" name="send" value="<?php echo JText::_('JBZOO_FORM_IMPORT'); ?>"
                               class="uk-button uk-button-primary" style="float: right;" />
                    </div>

                    <?php echo $jbform->renderRow($this->controls['apps'], 'JBZOO_IMPORT_CHOOSE_APP', 'appid'); ?>

                    <?php echo $jbform->renderRow($this->controls['types'], 'JBZOO_IMPORT_CHOOSE_TYPE', 'typeid'); ?>

                    <?php echo $jbform->renderRow($this->controls['key'], 'JBZOO_IMPORT_KEY', 'key'); ?>

                    <?php echo $jbform->renderRow($this->controls['create'], 'JBZOO_IMPORT_ITEMS_CREATE', 'create'); ?>

                    <?php echo $jbform->renderRow($this->controls['checkOptions'], 'JBZOO_IMPORT_CHECK_OPTIONS', 'checkOptions'); ?>

                    <?php echo $jbform->renderRow($this->controls['lose'], 'JBZOO_IMPORT_LOSE', 'lose'); ?>

                    <?php echo $jbform->renderRow($this->controls['createAlias'], 'JBZOO_IMPORT_CREATE_ALIAS', 'createAlias'); ?>

                    <?php echo $jbform->renderRow($this->controls['cleanPrice'], 'JBZOO_IMPORT_CLEAN_PRICE', 'cleanPrice'); ?>

                    <hr />

                    <ul id="fields-assign">
                        <?php foreach ($this->info['columns'] as $key => $column) : ?>
                            <li class="assign">
                                <?php
                                foreach ($this->controls['fields_types'] as $control) {
                                    echo str_ireplace('__name_placeholder__', $key, $control);
                                }
                                ?>

                                <span class="name">
                                <?php echo JText::_('JBZOO_COLUMN'); ?> #<?php echo($key + 1); ?>
                                    <?php echo !empty($column) ? ' - ' . $column : ''; ?>
                            </span>
                            </li>
                        <?php endforeach; ?>
                    </ul>

                    <div class="uk-form-row">
                        <input type="submit" name="send" value="<?php echo JText::_('JBZOO_FORM_IMPORT'); ?>"
                               class="uk-button uk-button-primary" style="float: right;" />
                    </div>

                </div>

            </fieldset>

        </form>

        <?php echo $this->partial('footer'); ?>
    </div>
</div>


<script type="text/javascript">
    (function ($) {

        function showSelects(val) {
            $('.type-select', $form).hide();
            if (val) {
                $('.type-select-' + val, $form).show();
            }
        }

        var prevParams = <?php echo json_encode($this->prevParams);?>,
            $form = $('#jbzooimport');

        $('#typeid').change(function () {
            showSelects($(this).val())
        });

        if (prevParams && prevParams.previousparams) {

            var prev = prevParams.previousparams;

            $('#appid').val(prev.appid);
            $('#typeid').val(prev.typeid).trigger('change');
            $('#lose').val(prev.lose);
            $('#key').val(prev.key);
            $('#create').val(prev.create);
            $('#checkOptions').val(prev.checkOptions);
            $('#createAlias').val(prev.createAlias);

            if (prev.cleanPrice == 1) {
                $('#cleanPrice').val(prev.cleanPrice);
            }

            if (prev.typeid) {
                var typeid = prev.typeid;
                $('#fields-assign select.type-select-' + prev.typeid).each(function (n, obj) {
                    $(obj).val(prevParams[typeid][n]);
                });
            }

        }

        function checkKey(value) {

            var result = false;
            var selects = $('#fields-assign select.type-select:visible');

            selects.each(function () {
                if ($(this).val() == value) {
                    result = true;
                    return false;
                } else if (value == 'jbprice' && $(this).val().indexOf(value) != -1 ||
                    value == 'jbprice' && $(this).val().indexOf('price_sku') != -1) {
                    result = true;
                    return false;
                }
            });
            return result;
        }

        $form.submit(function () {
            var selectedParam = $('#key').val();

            if (!$('#appid', $form).val()) {
                alert('<?php echo JText::_('JBZOO_IMPORT_NO_APP');?>');
                return false;
            }

            if (!$('#typeid', $form).val()) {
                alert('<?php echo JText::_('JBZOO_IMPORT_NO_TYPE');?>');
                return false;
            }

            if (selectedParam == <?php echo JBImportHelper::KEY_ID ?>) {
                var value = 'id';
                var result = checkKey(value);
                if (!result) {
                    alert('<?php echo JText::_('JBZOO_IMPORT_NO_SELECT_KEY');?>');
                    return false;
                }
            } else if (selectedParam == <?php echo JBImportHelper::KEY_NAME ?>) {
                var value = 'name';
                var result = checkKey(value);
                if (!result) {
                    alert('<?php echo JText::_('JBZOO_IMPORT_NO_SELECT_KEY');?>');
                    return false;
                }
            } else if (selectedParam == <?php echo JBImportHelper::KEY_ALIAS ?>) {
                var value = 'alias';
                var result = checkKey(value);
                if (!result) {
                    alert('<?php echo JText::_('JBZOO_IMPORT_NO_SELECT_KEY');?>');
                    return false;
                }
            } else if (selectedParam == <?php echo JBImportHelper::KEY_SKU ?>) {
                var value = 'jbprice';
                var result = checkKey(value);
                if (!result) {
                    alert('<?php echo JText::_('JBZOO_IMPORT_NO_SELECT_KEY');?>');
                    return false;
                }
            }

            return true;
        });

    })(jQuery);
</script>
