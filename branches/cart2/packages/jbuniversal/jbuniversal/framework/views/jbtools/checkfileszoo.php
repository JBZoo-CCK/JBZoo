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

$actionUrl = $this->app->jbrouter->admin(array('task' => 'checkFilesZoo'));

?>

<div class="uk-grid">
    <div id="sidebar" class="uk-width-1-6">
        <?php echo $this->partial('navigation'); ?>
    </div>

    <div class="uk-width-4-6">

        <h2><?php echo JText::_('JBZOO_ADMIN_TITLE_TOOLS_CHECKFILESZOO') ?></h2>

        <p><?php echo JText::_('JBZOO_ADMIN_PAGE_TOOLS_CHECKFILES_DESC'); ?></p>

        <h4><?php echo JText::_('JBZOO_ADMIN_PAGE_TOOLS_CHECKFILES_TITLE'); ?></h4>

        <ul>
            <li><?php echo JText::_('JBZOO_ADMIN_PAGE_TOOLS_CHECKFILES_1'); ?></li>
            <li><?php echo JText::_('JBZOO_ADMIN_PAGE_TOOLS_CHECKFILES_2'); ?></li>
        </ul>

        <a class="uk-button uk-button-primary jsCheckFilesReport" style="display: inline-block;"
           href="<?php echo $actionUrl; ?>"><?php echo JText::_('JBZOO_CHECKFILES_CHECK'); ?></a>

        <span class="checkfiles-loader" style="display: none;">
            <img src="<?php echo JUri::root(); ?>media/zoo/applications/jbuniversal/assets/img/misc/loader.gif" />
        </span>

        <div class="checkfiles-result"></div>

        <?php echo $this->partial('footer'); ?>
    </div>
</div>

<script type="text/javascript">
    jQuery(function ($) {
        $('.jsCheckFilesReport').click(function () {

            $('.checkfiles-loader').show();
            $('.checkfiles-result').empty();

            var date = new Date();
            $.get($(this).attr('href'), {
                'nocache': date.getMilliseconds()
            }, function (data) {
                $('.checkfiles-result').empty().html(data);
                $('.checkfiles-loader').hide();
            });

            return false;
        });
    });
</script>
