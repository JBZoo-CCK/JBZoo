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


$this->app->jbassets->jbzooLinks();

$align     = $this->app->jbitem->getMediaAlign($item, $layout);
$bootstrap = $this->app->jbbootstrap;
$rowClass  = $bootstrap->getRowClass();

?>

<?php if ($this->checkPosition('top')) : ?>
    <div class="item-top">
        <?php echo $this->renderPosition('top', array('style' => 'block')); ?>
    </div>
<?php endif; ?>

<div class="item-body clearfix">
    <div class="head-wrapper">
        <?php if ($this->checkPosition('image')) : ?>
            <div class="item-image pull-<?php echo $align; ?>">
                <?php echo $this->renderPosition('image'); ?>
            </div>
        <?php endif; ?>

        <?php if ($this->checkPosition('title')) : ?>
            <h1 class="item-title"><?php echo $this->renderPosition('title'); ?></h1>
        <?php endif; ?>

        <?php if ($this->checkPosition('description')) : ?>
            <div class="item-description">
                <?php echo $this->renderPosition('description', array('style' => 'block')); ?>
            </div>
        <?php endif; ?>
    </div>

    <div class="<?php echo $rowClass; ?> clearfix">
        <?php if ($this->checkPosition('price')) : ?>
            <div class="<?php echo $bootstrap->gridClass(2); ?> item-price jsCartModal">
                <?php echo $this->renderPosition('price'); ?>
            </div>
        <?php endif; ?>

        <?php if ($this->checkPosition('properties')) : ?>
            <div class="<?php echo $bootstrap->gridClass(2); ?> item-properties">
                <h3><?php echo JText::_('JBZOO_QUICKVIEW_SPECIFICATION'); ?></h3>
                <table class="table table-hover">
                    <?php echo $this->renderPosition('properties', array('style' => 'jbtable')); ?>
                </table>
            </div>
        <?php endif; ?>
    </div>

    <?php if ($this->checkPosition('bottom')) : ?>
        <div class="<?php echo $rowClass; ?> item-bottom">
            <div class="<?php echo $bootstrap->gridClass(12); ?>">
                <?php echo $this->renderPosition('bottom', array('style' => 'block')); ?>
            </div>
        </div>
    <?php endif; ?>

    <?php if ($this->checkPosition('related')) : ?>
        <div class="<?php echo $rowClass; ?> item-related">
            <div class="<?php echo $bootstrap->gridClass(12); ?>">
                <?php echo $this->renderPosition('related', array(
                    'labelTag' => 'h4',
                    'style'    => 'jbblock',
                )); ?>
            </div>
        </div>
    <?php endif; ?>
</div>

