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

$bootstrap = $this->app->jbbootstrap;
$rowClass  = $bootstrap->getRowClass();
?>

<div class="well clearfix">
    <?php if ($this->checkPosition('title')) : ?>
        <span class="item-title"><?php echo $this->renderPosition('title'); ?></span>
    <?php endif; ?>

    <div class="<?php echo $rowClass; ?>">
        <?php if ($this->checkPosition('image')) : ?>
            <div class="item-image <?php echo $bootstrap->gridClass(6); ?>">
                <?php echo $this->renderPosition('image'); ?>
            </div>
        <?php endif; ?>

        <?php if ($this->checkPosition('properties')) : ?>
            <div class="<?php echo $bootstrap->gridClass(6); ?>">
                <ul class="item-properties unstyled">
                    <?php echo $this->renderPosition('properties', array('style' => 'list')); ?>
                </ul>
            </div>
        <?php endif; ?>
    </div>

    <?php if ($this->checkPosition('text')) {
        echo $this->renderPosition('text', array('style' => 'block'));
    } ?>

    <?php if ($this->checkPosition('meta')) : ?>
        <ul class="item-metadata">
            <?php echo $this->renderPosition('meta', array('style' => 'list')); ?>
        </ul>
    <?php endif; ?>
</div>
