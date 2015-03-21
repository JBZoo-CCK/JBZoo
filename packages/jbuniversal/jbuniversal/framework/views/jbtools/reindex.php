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

$urlAction = $this->app->jbrouter->admin(array('task' => 'reindexStep'));
$this->app->jbassets->progressBar();

?>

<div class="uk-grid">
    <div id="sidebar" class="uk-width-1-6">
        <?php echo $this->partial('navigation'); ?>
    </div>

    <div class="uk-width-4-6">

        <?php if (0 == $this->total) : ?>
            <?php echo '<strong style="color:#a00;">' . JText::_('JBZOO_CHECKDB_NOITEMS') . '</strong>'; ?>

        <?php else: ?>

            <h2><?php echo JText::_('JBZOO_ADMIN_TITLE_TOOLS_REINDEX'); ?></h2>

            <p style="color:#a00;">
                <strong><?php echo JText::_('JBZOO_REINDEX_DESCRIPTION'); ?></strong>
            </p>

            <h4><?php echo JText::_('JBZOO_ADMIN_PAGE_TOOLS_REINDEX_TITLE_1'); ?></h4>
            <p><?php echo JText::_('JBZOO_ADMIN_PAGE_TOOLS_REINDEX_DESC_1'); ?></p>

            <h4><?php echo JText::_('JBZOO_ADMIN_PAGE_TOOLS_REINDEX_TITLE_2'); ?></h4>
            <ul>
                <li><?php echo JText::_('JBZOO_ADMIN_PAGE_TOOLS_REINDEX_1'); ?></li>
                <li><?php echo JText::_('JBZOO_ADMIN_PAGE_TOOLS_REINDEX_2'); ?></li>
                <li><?php echo JText::_('JBZOO_ADMIN_PAGE_TOOLS_REINDEX_3'); ?></li>
                <li><?php echo JText::_('JBZOO_ADMIN_PAGE_TOOLS_REINDEX_4'); ?></li>
            </ul>

            <p><?php echo JText::_('JBZOO_ADMIN_PAGE_TOOLS_REINDEX_DESC_2'); ?></p>

            <div class="jsProgressBar"></div>

            <div class="statistic">
                <h3><?php echo JText::_('JBZOO_REINDEX_STATISTIC'); ?>: </h3>
                <ul>
                    <li><strong><?php echo JText::_('JBZOO_REINDEX_PROGRESS'); ?>:</strong> <span
                            class="js-progress">0</span>%
                    </li>
                    <li><strong><?php echo JText::_('JBZOO_REINDEX_STEP_CURRENT'); ?>:</strong> <span
                            class="js-step">0</span>
                    </li>
                    <li><strong><?php echo JText::_('JBZOO_REINDEX_INDEXED'); ?>:</strong> <span
                            class="js-current">0</span>
                    </li>
                    <li><strong><?php echo JText::_('JBZOO_REINDEX_TOTAL'); ?>:</strong> <span
                            class="js-total"><?php echo $this->total; ?></span>
                    </li>
                    <li><strong><?php echo JText::_('JBZOO_REINDEX_STEP_SIZE'); ?>:</strong> <span
                            class="js-stepsize"><?php echo $this->indexStep; ?></span>
                    </li>
                </ul>
                <ul>
                    <li><strong><?php echo JText::_('JBZOO_REINDEX_TIME_PASSED'); ?>:</strong> <span
                            class="js-timepassed">00:00</span>
                    </li>
                    <li><strong><?php echo JText::_('JBZOO_REINDEX_TIME_REMAINING'); ?>:</strong> <span
                            class="js-timeremaining">00:00</span>
                    </li>
                </ul>
            </div>

            <div class="error-block jsErrorBlockWrapper" style="display: none;">
                <hr />
                <h3><em><?php echo JText::_('JBZOO_PROGRESSBAR_ERROR_REPORTING'); ?></em></h3>

                <div class="jsErrorBlock"></div>
            </div>

        <?php endif; ?>

        <?php echo $this->partial('footer'); ?>
    </div>
</div>

<script type="text/javascript">
    jQuery(function ($) {

        var totalLines = 0;

        $('.jsProgressBar').JBZooProgressBar({
            'text_start_confirm': "<?php echo JText::_('JBZOO_PROGRESSBAR_START_CONFIRM');?>",
            'text_stop_confirm' : "<?php echo JText::_('JBZOO_PROGRESSBAR_STOP_CONFIRM');?>",
            'text_complete'     : "<?php echo JText::_('JBZOO_PROGRESSBAR_COMPLETE');?>",
            'text_start'        : "<?php echo JText::_('JBZOO_PROGRESSBAR_START');?>",
            'text_stop'         : "<?php echo JText::_('JBZOO_PROGRESSBAR_STOP');?>",
            'text_ready'        : "<?php echo JText::_('JBZOO_PROGRESSBAR_READY');?>",
            'url'               : "<?php echo $urlAction;?>",
            'autoStart'         : "<?php echo (bool)$this->app->jbrequest->get('autostart', 0); ?>",

            'onRequest': function (data) {
                totalLines = totalLines + data.lines;
                $('.js-totallines').text(totalLines);
                $.each(data, function (key, data) {
                    $('.js-' + key).text(data);
                });
            },

            'onStop': function () {
                $('.js-timeremaining').text('00:00');
            },

            'onTimer': function (data) {
                $('.js-timepassed').text(data.passed);
                $('.js-timeremaining').text(data.remaining);
            }
        });
    });
</script>
