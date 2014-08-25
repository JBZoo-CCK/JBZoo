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

$actionUrl = $this->app->jbrouter->admin(array('task' => 'categoriesSteps'));
$jbform = $this->app->jbform;
?>

<div class="uk-grid">
    <div id="sidebar" class="uk-width-1-6">
        <?php echo $this->partial('navigation'); ?>
    </div>

    <div class="uk-width-4-6">
        <form class="jbzoo-import-fields jbadminform uk-form uk-form-horizontal" id="jbzooimport"
              action="<?php echo $actionUrl; ?>" name="jbzooimport" method="post" enctype="multipart/form-data">

            <h2><?php echo JText::_('JBZOO_ADMIN_TITLE_IMPORT_ITEMS_FIELDS'); ?></h2>

            <fieldset class="items">

                <div class="assign-group">
                    <div class="uk-form-row">
                        <input type="submit" name="send" value="<?php echo JText::_('JBZOO_FORM_IMPORT'); ?>"
                               class="uk-button uk-button-primary" style="float: right;" />
                    </div>

                    <?php echo $jbform->renderRow($this->controls['apps'], 'JBZOO_IMPORT_CHOOSE_APP', 'appid'); ?>

                    <?php echo $jbform->renderRow($this->controls['key'], 'JBZOO_IMPORT_KEY', 'key'); ?>

                    <?php echo $jbform->renderRow($this->controls['create'], 'JBZOO_IMPORT_CATEGORIES_CREATE', 'create'); ?>

                    <?php echo $jbform->renderRow($this->controls['lose'], 'JBZOO_IMPORT_LOSE', 'lose'); ?>

                    <?php echo $jbform->renderRow($this->controls['createAlias'], 'JBZOO_IMPORT_CREATE_ALIAS', 'createAlias'); ?>

                    <hr />

                    <ul id="fields-assign">
                        <?php foreach ($this->info['columns'] as $key => $column) : ?>
                            <li class="assign">
                                <?php echo $this->controls['fields_types']; ?>
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
    </div>
</div>


<script type="text/javascript">
    (function ($) {

        $('.type-select').show(); // template hack

        var prevParams = <?php echo json_encode($this->prevParams);?>,
            $form = $('#jbzooimport');

        if (prevParams) {
            $('#appid').val(prevParams.appid);
            $('#lose').val(prevParams.lose);
            if (prevParams.create) {
                $('#create').val(prevParams.create);
            }

            $('#createAlias').val(prevParams.createalias);

            $('#key').val(prevParams.key);

            if (prevParams.assign) {
                $('#fields-assign select.type-select').each(function (n, obj) {
                    $(this).val(prevParams.assign[n]);
                });
            }
        }

        $form.submit(function () {
            // TODO validate categoryKey
            if (!$('#appid', $form).val()) {
                alert('<?php echo JText::_('JBZOO_IMPORT_NO_APP');?>');
                return false;
            }

            return true;
        });

    })(jQuery);
</script>


