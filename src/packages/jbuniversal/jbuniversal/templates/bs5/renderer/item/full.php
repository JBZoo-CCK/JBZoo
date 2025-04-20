<?php
use Joomla\CMS\Helper\ModuleHelper;
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

// $document = JFactory::getDocument();
// $zoo = App::getInstance('zoo');
// $align     = $this->app->jbitem->getMediaAlign($item, $layout);
// // $tabsId    = $this->app->jbstring->getId('tabs');
// // $itemUrl = $this->app->route->item($item);

// $align      = $this->app->jbitem->getMediaAlign($item, $layout);
// $tabsId     = $this->app->jbstring->getId('tabs');
// $bootstrap = $this->app->jbbootstrap;
// $rowClass   = $bootstrap->getRowClass();
?>

<article class="clearfix">

<?php if ($this->checkPosition('title')) : ?>
    <h1 class="item-title"><?php echo $this->renderPosition('title'); ?></h1>
<?php endif; ?>

<?php if ($this->checkPosition('meta')) : ?>
    <div class="iteminfo">
                        <ul class="unstyled">
                            <?php echo $this->renderPosition('meta', array('style' => 'list')); ?>
                        </ul>
    </div>
<?php endif; ?>


<?php if ($this->checkPosition('image')) : ?>
                <div class="item-image">
                    <?php  echo $this->renderPosition('image');?>
                </div>
<?php endif; ?>



<?php if ($this->checkPosition('buttons')) : ?>
                        <?php echo $this->renderPosition('buttons', array('style' => 'block')); ?>
<?php endif; ?>
  

<?php if ($this->checkPosition('pretext')) : ?>
                <div class="pretext">
                    <?php echo $this->renderPosition('pretext'); ?>
                </div>
<?php endif; ?>

<?php if ($this->checkPosition('fulltext')) : ?>
                <div class="fulltext">
                    <?php echo $this->renderPosition('fulltext'); ?>
                </div>
<?php endif; ?>

<?php if ($this->checkPosition('images')) : ?>
                <div class="images">
                    <?php echo $this->renderPosition('images'); ?>
                </div>
<?php endif; ?>

<?php if ($this->checkPosition('tags')) : ?>
                <div class="tags">
                <ul>   <?php echo $this->renderPosition('tags', array('style' => 'list')); ?> </ul>
                </div>
<?php endif; ?>


<?php if ($this->checkPosition('video')) : ?>
                <div class="oldvideo">
                <?php echo $this->renderPosition('video'); ?>
                </div>
<?php endif; ?>

<?php // dd($item); ?>


<?php if ($this->checkPosition('from')) : ?>
                <div class="from">
             <ul>   <?php echo $this->renderPosition('from', array('style' => 'list')); ?> </ul>
                </div>
<?php endif; ?>


<?php if ($this->checkPosition('related')) : ?>
                <div class="from">
             <ul>   <?php echo $this->renderPosition('from', array('style' => 'list')); ?> </ul>
                </div>
<?php endif; ?>


<?php if ($this->checkPosition('social')) : ?>
                <?php echo $this->renderPosition('social', array('style' => 'block')); ?>
<?php endif; ?>
    
</article>