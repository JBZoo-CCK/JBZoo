<?php
/**
 * JBZoo Application
 *
 * This file is part of the JBZoo CCK package.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package    Application
 * @license    GPL-2.0
 * @copyright  Copyright (C) JBZoo.com, All rights reserved.
 * @link       https://github.com/JBZoo/JBZoo
 * @author     Denis Smetannikov <denis@jbzoo.com>
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

$actionUrl = $this->app->jbrouter->admin(array('task' => 'checkFiles'));
$removeUrl = $this->app->jbrouter->admin(array('task' => 'removeUnversionFiles'));
?>
<div class="uk-grid">
    <div id="sidebar" class="uk-width-1-6">
        <?php echo $this->partial('navigation'); ?>
    </div>

    <div class="uk-width-4-6">

        <h2><?php echo JText::_('JBZOO_ADMIN_TITLE_TOOLS_CHECKFILES'); ?></h2>

        <p><?php echo JText::_('JBZOO_ADMIN_PAGE_TOOLS_CHECKFILES_DESC'); ?></p>

        <h4><?php echo JText::_('JBZOO_ADMIN_PAGE_TOOLS_CHECKFILES_TITLE'); ?></h4>

        <ul>
            <li><?php echo JText::_('JBZOO_ADMIN_PAGE_TOOLS_CHECKFILES_1'); ?></li>
            <li><?php echo JText::_('JBZOO_ADMIN_PAGE_TOOLS_CHECKFILES_2'); ?></li>
        </ul>

        <a class="uk-button uk-button-primary jsCheckFilesReport"
           href="#check"><?php echo JText::_('JBZOO_CHECKFILES_CHECK'); ?></a>

        <a class="uk-button uk-button-small  uk-button-danger jsCheckFilesRemove"
           href="#remove"><?php echo JText::_('JBZOO_CHECKFILES_REMOVE_UNVERSION'); ?></a>

        <span class="checkfiles-loader" style="display: none;">
            <img src="<?php echo JUri::root(); ?>media/zoo/applications/jbuniversal/assets/img/misc/loader.gif" />
        </span>

        <div class="checkfiles-result"></div>

        <?php echo $this->partial('footer'); ?>
    </div>
</div>

<script type="text/javascript">
    jQuery(function ($) {

        var alert1 = "<?php echo JText::_('JBZOO_CHECKFILES_REMOVE_UNVERSION_ALERT_1'); ?>",
            alert2 = "<?php echo JText::_('JBZOO_CHECKFILES_REMOVE_UNVERSION_ALERT_2'); ?>",
            alert3 = "<?php echo JText::_('JBZOO_CHECKFILES_REMOVE_UNVERSION_ALERT_3'); ?>",
            actionUrl = "<?php echo $actionUrl; ?>",
            removeUrl = "<?php echo $removeUrl; ?>";

        $('.jsCheckFilesReport').click(function () {

            $('.checkfiles-loader').show();
            $('.checkfiles-result').empty();

            JBZoo.ajax({
                'url'     : actionUrl,
                'dataType': 'html',
                'success' : function (data) {
                    $('.checkfiles-result').empty().html(data);
                    $('.checkfiles-loader').hide();
                }
            });

            return false;
        });

        $('.jsCheckFilesRemove').click(function () {

            JBZoo.confirm(alert1, function () {
                setTimeout(function () {

                    JBZoo.confirm(alert2, function () {
                        setTimeout(function () {

                            JBZoo.confirm(alert3, function () {
                                $('.checkfiles-loader').show();
                                $('.checkfiles-result').empty();

                                JBZoo.ajax({
                                    'url'     : removeUrl,
                                    'dataType': 'html',
                                    'success' : function (data) {
                                        $('.checkfiles-result').empty().html(data);
                                        $('.checkfiles-loader').hide();
                                    }
                                });
                            });

                        }, 500);
                    });

                }, 500);
            });

            return false;
        });

    });
</script>