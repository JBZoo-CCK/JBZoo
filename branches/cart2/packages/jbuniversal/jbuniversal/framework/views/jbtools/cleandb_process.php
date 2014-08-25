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

$urlAjax = $this->app->jbrouter->admin(array('controller' => 'manager', 'format' => 'raw', 'task' => 'cleandbstep'));

?>

<div class="uk-grid">
    <div id="sidebar" class="uk-width-1-6">
        <?php echo $this->partial('navigation'); ?>
    </div>

    <div class="uk-width-4-6">

        <h2><?php echo JText::_('JBZOO_ADMIN_TITLE_TOOLS_CLEANDB_PROCESS'); ?></h2>

        <p style="color:#a00;">
            <strong><?php echo JText::_('JBZOO_CLEANDB_NO_CLOSE'); ?></strong>
        </p>

        <div class="uk-progress uk-progress-striped uk-active">
            <div class="uk-progress-bar jsProgressBar"
                 style="width:0"><?php echo JText::_('JBZOO_PROGRESSBAR_READY'); ?></div>
        </div>

        <p>
            <em><?php echo JText::_('JBZOO_CLEANDB_LAST_ACTION'); ?>:</em> <span class="jsCurrentAction">-</span>
        </p>

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

        step('<?php echo $urlAjax;?>');

        var $progressWrp = $('.uk-progress'),
            $progressbar = $('.jsProgressBar'),
            $currentAction = $('.jsCurrentAction'),
            steps = <?php echo $this->steps;?>,
            stepSize = 100 / steps;

        function step(url) {

            $.getJSON(url)
                .success(function (data) {

                    var width = (data.step * stepSize).toFixed(2);

                    if (data.error) {
                        showError(data.error);
                    }

                    if (data.message) {
                        $currentAction.html(data.message);
                    }

                    if (data.step >= steps || data.forward) {
                        $progressbar.html('<?php echo JText::_('JBZOO_PROGRESSBAR_COMPLETE');?>');
                        $progressWrp.removeClass('uk-progress-striped uk-active');
                        $progressbar.css('width', '100%');
                    } else {
                        $progressbar.html(width + '%');
                    }

                    if (width < 100) {
                        $progressbar.css('width', width + '%');
                    }

                    if (data.redirect) {
                        step(data.redirect);
                    }
                })
                .error(function (result) {
                    showError(result.responseText);
                });
        }

        function showError(message) {
            $('.jsErrorBlockWrapper').show();
            $('.jsErrorBlock').html(message);
            $progressWrp.removeClass('uk-active');
        }

    });
</script>
