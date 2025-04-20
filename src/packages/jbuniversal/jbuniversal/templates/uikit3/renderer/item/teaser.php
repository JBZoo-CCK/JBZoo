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

$align = $this->app->jbitem->getMediaAlign($item, $layout);
?>
<div class="uk-card uk-card-default uk-card-body">
    <?php if ($this->checkPosition('title')) : ?>
        <h4 class="item-title"><?php echo $this->renderPosition('title'); ?></h4>
    <?php endif; ?>

    <div >
        <div >
            <?php if ($this->checkPosition('image')) : ?>
                <div class="item-image">
                    <?php echo $this->renderPosition('image'); ?>
                </div>
            <?php endif; ?>
        </div>
        <div>
            <?php if ($this->checkPosition('properties')) : ?>
                <div class="item-properties">
                    <ul class="uk-list">
                        <?php echo $this->renderPosition('properties', array('style' => 'list')); ?>
                    </ul>
                </div>
            <?php endif; ?>
        </div>
        <div >
            <div class="item-buttons">
                <?php echo $this->renderPosition('buttons'); ?>
            </div>
        </div>
    </div>
</div>