<?php
/**
 * JBZoo App is universal Joomla CCK, application for YooTheme Zoo component
 *
 * @package     jbzoo
 * @version     2.x Pro
 * @author      JBZoo App http://jbzoo.com
 * @copyright   Copyright (C) JBZoo.com,  All rights reserved.
 * @license     http://jbzoo.com/license-pro.php JBZoo Licence
 * @coder       Vitaliy Yanovskiy <joejoker@jbzoo.com>
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

$urlAction = $this->app->jbrouter->admin(array('task' => 'writeStep'));
$urlSettings = $this->app->jbrouter->admin(array('controller' => 'jbconfig', 'task' => 'yandexyml'));
$strFile = JText::_('JBZOO_YML_FILE_URL');

$this->app->jbassets->progressBar();

?>
<div class="uk-grid">
    <div id="sidebar" class="uk-width-1-6">
        <?php echo $this->partial('navigation'); ?>
    </div>

    <div class="uk-width-4-6">

        <?php if (0 == $this->total) : ?>
            <?php echo '<strong style="color:#a00;">' . JText::_('JBZOO_YML_NOITEMS') . '</strong><br>' .
                JText::_('JBZOO_YML_URL_SETTINGS') . ': <a href="' . $urlSettings . '">' . JText::_('JBZOO_YML_URL_SETTINGS_DESC') . '</a>'; ?>
        <?php else: ?>

            <h2><?php echo JText::_('JBZOO_ADMIN_TITLE_EXPORT_YML'); ?></h2>

            <p><strong><em><?php echo JText::_('JBZOO_YML_DESCRIPTION'); ?></em></strong></p>

            <strong><?php echo JText::_('JBZOO_YML_ATTENTION'); ?></strong>
            <ul>
                <li><?php echo JText::_('JBZOO_YML_ATTENTION_1'); ?></li>
                <li><?php echo JText::_('JBZOO_YML_ATTENTION_2'); ?></li>
                <li><?php echo JText::_('JBZOO_YML_ATTENTION_3'); ?></li>
                <li>
                    <a target="_blank"
                       href="<?php echo $this->app->jbrouter->admin(array('controller' => 'jbconfig', 'task' => 'yandexYml')); ?>">
                        <?php echo JText::_('JBZOO_YML_ATTENTION_4'); ?>
                    </a>
                </li>
            </ul>


            <div class="jsProgressBar"></div>

            <div class="statistic">
                <h3><?php echo JText::_('JBZOO_REINDEX_STATISTIC'); ?>: </h3>
                <ul>
                    <li><strong><?php echo JText::_('JBZOO_REINDEX_PROGRESS'); ?>:</strong>
                        <span class="js-progress">0</span>%
                    </li>
                    <li><strong><?php echo JText::_('JBZOO_REINDEX_STEP_CURRENT'); ?>:</strong>
                        <span class="js-step">0</span>
                    </li>
                    <li><strong><?php echo JText::_('JBZOO_YML_INDEXED'); ?>:</strong>
                        <span class="js-ymlcount">0</span>
                    </li>
                    <li><strong><?php echo JText::_('JBZOO_REINDEX_TOTAL'); ?>:</strong>
                        <span class="js-total"><?php echo $this->total; ?></span>
                    </li>
                    <li><strong><?php echo JText::_('JBZOO_REINDEX_STEP_SIZE'); ?>:</strong>
                        <span class="js-stepsize"><?php echo $this->indexStep; ?></span>
                    </li>
                </ul>
                <ul>
                    <li><strong><?php echo JText::_('JBZOO_REINDEX_TIME_PASSED'); ?>:</strong>
                        <span class="js-timepassed">00:00</span>
                    </li>
                    <li><strong><?php echo JText::_('JBZOO_REINDEX_TIME_REMAINING'); ?>:</strong>
                        <span class="js-timeremaining">00:00</span>
                    </li>
                </ul>
            </div>

            <div class="error-block jsErrorBlockWrapper" style="display: none;">
                <hr />
                <h3><em><?php echo JText::_('JBZOO_PROGRESSBAR_ERROR_REPORTING'); ?></em></h3>

                <div class="jsErrorBlock"></div>
            </div>

            <div class="url-file" style="display: none; color: #a00;"></div>
        <?php endif; ?>

        <?php echo $this->partial('footer'); ?>
    </div>
</div>

<script type="text/javascript">
    jQuery(function ($) {

        var totalLines = 0,
            pAjax = "<?php echo $this->app->jbyml->getPath(false);?>";

        $('.jsProgressBar').JBZooProgressBar({
            'text_start_confirm': "<?php echo JText::_('JBZOO_PROGRESSBAR_START_CONFIRM');?>",
            'text_stop_confirm' : "<?php echo JText::_('JBZOO_PROGRESSBAR_STOP_CONFIRM');?>",
            'text_complete'     : "<?php echo JText::_('JBZOO_PROGRESSBAR_COMPLETE');?>",
            'text_start'        : "<?php echo JText::_('JBZOO_PROGRESSBAR_START');?>",
            'text_stop'         : "<?php echo JText::_('JBZOO_PROGRESSBAR_STOP');?>",
            'text_ready'        : "<?php echo JText::_('JBZOO_PROGRESSBAR_READY');?>",
            'url'               : "<?php echo $urlAction;?>",

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

            'onFinal': function (callback) {
                callback();
                $('.url-file').show().html(
                    '<strong><?php echo $strFile ?>: </strong><a href="' + pAjax + '" target="_blank">' + pAjax + '</a>'
                );
            },

            'onTimer': function (data) {
                $('.js-timepassed').text(data.passed);
                $('.js-timeremaining').text(data.remaining);
            }
        });
    });
</script>
