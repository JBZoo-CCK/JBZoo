<?php
/**
 * JBZoo App is universal Joomla CCK, application for YooTheme Zoo component
 *
 * @package     jbzoo
 * @version     2.x Pro
 * @author      JBZoo App http://jbzoo.com
 * @copyright   Copyright (C) JBZoo.com,  All rights reserved.
 * @license     http://jbzoo.com/license-pro.php JBZoo Licence
 * @coder       Sergey Kalistratov <kalistratov.s.m@gmail.com>
 */


// no direct access
defined('_JEXEC') or die('Restricted access');

?>

<div id="edit" style="display:none">
    <h3><?php echo JText::_('Edit comment'); ?></h3>

    <form class="style short" method="post" action="<?php echo $this->app->link(array('controller' => 'comment', 'task' => 'edit')); ?>">

        <div class="content jbzoo-divider">
            <textarea name="content" rows="5" cols="80"></textarea>
        </div>

        <div class="actions">
            <input name="submit" class="jbbutton green" type="submit" value="<?php echo JText::_('Save comment'); ?>" accesskey="s" />
            <a class="comment-cancelEdit jbbutton" href="#edit"><?php echo JText::_('Cancel'); ?></a>
        </div>

        <input type="hidden" name="comment_id" value="0" />
        <input type="hidden" name="redirect" value="<?php echo str_replace('&', '&amp;', $this->app->request->getString('REQUEST_URI', '', 'server')); ?>" />
        <?php echo $this->app->html->_('form.token'); ?>

    </form>
</div>
