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