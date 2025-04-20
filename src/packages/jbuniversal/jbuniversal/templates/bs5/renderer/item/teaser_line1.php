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

$align     = $this->app->jbitem->getMediaAlign($item, $layout);
$bootstrap = $this->app->jbbootstrap;
$rowClass  = $bootstrap->getRowClass();
?>


<div class="row <?php echo $rowClass; ?>">


    <?php if ($this->checkPosition('image')) : ?>
        <div class="col-md-6 teaserimgcentr">
            <div class="item-image">
                <?php echo $this->renderPosition('image'); ?>
            </div>

        </div>
    <?php endif; ?>

    <div class="col-md-6">


    <?php if ($this->checkPosition('title')) : ?>
    <h3 class="item-title"><?php echo $this->renderPosition('title'); ?></h3>
    <?php endif; ?>

    
<?php if ($this->checkPosition('properties')) : ?>
<div class="col-md-12 teaserinfoarticle">
                <div class="item-properties">
                    <ul class="list-unstyled">
                        <?php echo $this->renderPosition('properties', array('style' => 'list')); ?>
                    </ul>
                </div>
</div>
<?php endif; ?>




        <?php if ($this->checkPosition('price')) : ?>
            <div class="item-price">
                <?php echo $this->renderPosition('price', array('style' => 'block')); ?>
            </div>
        <?php endif; ?>

        <?php if ($this->checkPosition('text')) : ?>
            <div class="item-text">
                <?php echo $this->renderPosition('text', array('style' => 'block')); ?>
            </div>
        <?php endif; ?>

        <?php if ($this->checkPosition('quick-view')) : ?>
            <div class="item-quick-view">
                <?php echo $this->renderPosition('quick-view', array('style' => 'block')); ?>
            </div>
        <?php endif; ?>

        <?php if ($this->checkPosition('buttons')) : ?>
            <div class="item-buttons clearfix">
                <?php echo $this->renderPosition('buttons'); ?>
            </div>
        <?php endif; ?>
    </div>
</div>
