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
    <?php if((int)$this->config->get('likes_enabled', 1)) : ?>
        <div class="row">
            <label for="<?php echo $this->getControlName('likes_enabled');?>"><?php echo JText::_('JBZOO_SOCIAL_LIKES');?></label>
            <?php echo $this->app->html->_('select.booleanlist',
                'elements[' . $this->identifier . '][likes_enabled]'
                . $this->getControlName('likes_enabled'), null, $this->get('likes_enabled', 1)); ?>
        </div>
    <?php endif ;?>

    <?php if((int)$this->config->get('bookmarks_enabled', 1)) : ?>
        <div class="row">
            <label for="<?php echo $this->getControlName('bookmarks_enabled');?>"><?php echo JText::_('JBZOO_SOCIAL_BOOKMARKS');?></label>
            <?php echo $this->app->html->_('select.booleanlist',
                'elements[' . $this->identifier . '][bookmarks_enabled]'
                . $this->getControlName('bookmarks_enabled'), null, $this->get('bookmarks_enabled', 1)); ?>
        </div>
    <?php endif ;?>

    <?php if((int)$this->config->get('complex_enabled', 1)) : ?>
        <div class="row">
            <label for="<?php echo $this->getControlName('complex_enabled');?>"><?php echo JText::_('JBZOO_SOCIAL_COMPLEX');?></label>
            <?php echo $this->app->html->_('select.booleanlist',
                'elements[' . $this->identifier . '][complex_enabled]'
                . $this->getControlName('complex_enabled'), null, $this->get('complex_enabled', 1)); ?>
        </div>
    <?php endif ;?>
</div>