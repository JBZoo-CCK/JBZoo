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
