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
<div class="wrapper-item-desc">
    <?php if ($this->checkPosition('image')) : ?>
        <div class="item-image align-<?php echo $params->get('items_image_align', 'left') ?>">
            <?php echo $this->renderPosition('image'); ?>
        </div>
    <?php endif; ?>

    <?php if ($this->checkPosition('description') || $this->checkPosition('title')) : ?>
        <div class="item-wrapper-desc">
            <?php if ($this->checkPosition('title')) : ?>
                <div class="item-title"><?php echo $this->renderPosition('title'); ?></div>
            <?php endif; ?>

            <?php if ($this->checkPosition('description')) : ?>
                <div class="item-description"><?php echo $this->renderPosition('description'); ?></div>
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <?php if ($this->checkPosition('properties')) : ?>
        <div class="product-props">
            <ul>
                <?php echo $this->renderPosition('properties', array('style' => 'list')); ?>
            </ul>
        </div>
    <?php endif; ?>
</div>
<div class="item-links"><?php echo $this->renderPosition('links', array('style' => 'pipe')); ?></div>

<?php echo JBZOO_CLR; ?>
