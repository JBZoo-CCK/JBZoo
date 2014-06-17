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


?>
<div>
<?php if((int)$this->config->get('fb_comments_enabled', 1)) : ?>
    <div class="row">
        <label for="<?php echo $this->getControlName('fb_comments_enabled');?>"><?php echo JText::_('JBZOO_COMMENTS_FB');?></label>
        <?php echo $this->app->html->_('select.booleanlist',
                'elements[' . $this->identifier . '][fb_comments_enabled]'
                . $this->getControlName('fb_comments_enabled'), null, $this->get('fb_comments_enabled', 1)); ?>
    </div>
<?php endif ;?>

<?php if((int)$this->config->get('vk_comments_enabled', 1)) : ?>
    <div class="row">
        <label for="<?php echo $this->getControlName('vk_comments_enabled');?>"><?php echo JText::_('JBZOO_COMMENTS_VK');?></label>
        <?php echo $this->app->html->_('select.booleanlist',
            'elements[' . $this->identifier . '][vk_comments_enabled]'
            . $this->getControlName('vk_comments_enabled'), null, $this->get('vk_comments_enabled', 1)); ?>
    </div>
<?php endif ;?>
</div>