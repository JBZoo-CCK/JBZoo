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

$urlPostAjax = $this->app->jbrouter->admin(array('task' => 'migrateAjax'));
?>

<div class="uk-grid">
    <div id="sidebar" class="uk-width-1-6">
        <?php echo $this->partial('navigation'); ?>
    </div>

    <div class="uk-width-4-6">

        <h2><?php echo JText::_('JBZOO_MIGRATE_CONFIG_TITLE'); ?></h2>

        <p style="color:#a00;">
            <strong><?php echo JText::_('JBZOO_MIGRATE_STEPS_DESCRIPTION'); ?></strong>
        </p>

        <p><?php echo JText::_('JBZOO_PROGRESSBAR_FATAL'); ?></p>

        <div class="jsProgressBar progress jbadminform"></div>
        <p>&nbsp;</p>

        <div class="error-block jsErrorBlockWrapper" style="display: none;">
            <hr />
            <h3><em><?php echo JText::_('JBZOO_PROGRESSBAR_ERROR_REPORTING'); ?></em></h3>

            <div class="jsErrorBlock"></div>
        </div>

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
            'text_wait'         : "<?php echo JText::_('JBZOO_PROGRESSBAR_COMPLETE');?>",
            'url'               : "<?php echo $urlPostAjax;?>",
            'autoStart'         : true,

            'onRequest': function (data) {
                dump(data);
                $.each(data, function (key, data) {
                    $('.js-' + key).text(data);
                });
            },

            'onStop': function () {
                $('.js-timeremaining').text('00:00');
                $('.jsStart, .jsStop').hide();
            },

            'onFinal': function (callback) {
                $('.jsLoader').hide();
            },

            'onTimer': function (data) {
                $('.js-timepassed').text(data.passed);
                $('.js-timeremaining').text(data.remaining);
            }
        });
    });
</script>