<?php
/**
 * JBZoo App is universal Joomla CCK, application for YooTheme Zoo component
 * @package     jbzoo
 * @version     2.x Pro
 * @author      JBZoo App http://jbzoo.com
 * @copyright   Copyright (C) JBZoo.com,  All rights reserved.
 * @license     http://jbzoo.com/license-pro.php JBZoo Licence
 * @coder       Alexander Oganov <t_tapak@yahoo.com>
 */

// no direct access
defined('_JEXEC') or die('Restricted access');


?>

<div class="jbadvert-wrapper">

    <div class="jbadvert-status">
        <?php echo JText::sprintf('JBZOO_JBADVERT_IS_MODIFIED', $this->_getPrice()->html(), $this->_getLastModified()); ?>
    </div>

    <?php if ($this->_getMode() == ElementJBAdvert::MODE_EXPIREDATE) : ?>
        <div class="jbadvert-EXPIREDATE">
            <?php echo JText::sprintf('JBZOO_JBADVERT_CURRENT_EXPIREDATE', $this->app->jbdate->toHuman($this->getItem()->publish_down)); ?>
        </div>
    <?php endif; ?>

    <?php echo $this->app->jbhtml->hidden($this->getControlName('zoohack'), 1); ?>

</div>
