<?php
/**
 * JBZoo App is universal Joomla CCK, application for YooTheme Zoo component
 *
 * @package     jbzoo
 * @version     2.x Pro
 * @author      JBZoo App http://jbzoo.com
 * @copyright   Copyright (C) JBZoo.com,  All rights reserved.
 * @license     http://jbzoo.com/license-pro.php JBZoo Licence
 * @coder       Alexander Oganov <t_tapak@yahoo.com>
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

$this->app->jbassets->progressBar();

$urlAjax     = $this->app->jbrouter->admin(array('task' => 'doStep'));
$urlPostAjax = $this->app->jbrouter->admin(array('task' => 'lastStep')); ?>

<div class="uk-grid">
    <div id="sidebar" class="uk-width-1-6">
        <?php echo $this->partial('navigation'); ?>
    </div>

    <div class="uk-width-4-6">

        <h2><?php echo JText::_('JBZOO_JBMIGRATE_CONFIG_TITLE'); ?></h2>

        <p><strong><?php echo JText::_('JBZOO_JBMIGRATE_ATTENTION'); ?></strong></p>

        <ul>
            <li><?php echo JText::_('JBZOO_JBMIGRATE_ATTENTION_1'); ?></li>
            <li><?php echo JText::_('JBZOO_JBMIGRATE_ATTENTION_2'); ?></li>
            <li><?php echo JText::_('JBZOO_JBMIGRATE_ATTENTION_3'); ?></li>
        </ul>
        <div class="jsProgressBar progress jbadminform"></div>
        <p>&nbsp;</p>

        <?php echo $this->app->jbform->render('migrate_config', array(
            'action'     => $this->app->jbrouter->admin(array('task' => 'lastStep')),
            'showSubmit' => true,
            'submit'     => JText::_('JBZOO_FORM_NEXT')
        )); ?>

        <?php echo $this->partial('footer'); ?>
    </div>
</div>

<script type="text/javascript">
    jQuery(function ($) {

        var postAjax = "<?php echo $urlPostAjax;?>";

        $('.jsProgressBar').JBZooProgressBar({
            'text_start_confirm': "<?php echo JText::_('JBZOO_PROGRESSBAR_START_CONFIRM');?>",
            'text_stop_confirm' : "<?php echo JText::_('JBZOO_PROGRESSBAR_STOP_CONFIRM');?>",
            'text_complete'     : "<?php echo JText::_('JBZOO_PROGRESSBAR_COMPLETE');?>",
            'text_start'        : "<?php echo JText::_('JBZOO_PROGRESSBAR_START');?>",
            'text_stop'         : "<?php echo JText::_('JBZOO_PROGRESSBAR_STOP');?>",
            'text_ready'        : "<?php echo JText::_('JBZOO_PROGRESSBAR_READY');?>",
            'text_wait'         : "<?php echo JText::_('JBZOO_PROGRESSBAR_WAIT_CLEAN');?>",
            'url'               : "<?php echo $urlAjax;?>",

            'onRequest': function (data) {
                $.each(data, function (key, data) {
                    $('.js-' + key).text(data);
                });
            },

            'onStop': function () {
                $('.js-timeremaining').text('00:00');
                $('.jsStart, .jsStop').hide();
            },

            'onFinal': function (callback) {
                $('.jsLoader').show();
                $.get(postAjax, { // TODO: replace to jbajax
                    'nocache': (new Date()).getMilliseconds()
                }, function () {
                    callback();
                    $('.jsLoader').hide();
                    JBZoo.confirm("<?php echo JText::_('JBZOO_IMPORT_ITEMS_NEED_REINDEX'); ?>",
                        function () {
                            window.location.href = "<?php echo $this->app->jbrouter->admin(array(
                                 'controller' => 'jbmigrate',
                                 'task'       => 'do'
                                 )); ?>";
                        });
                });
            },

            'onTimer': function (data) {
                $('.js-timepassed').text(data.passed);
                $('.js-timeremaining').text(data.remaining);
            }
        });
    });
</script>