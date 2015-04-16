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
$align = $this->app->jbitem->getMediaAlign($item, $layout);
?>

<?php if ($this->checkPosition('top')) : ?>
    <div class="item-top">
        <?php echo $this->renderPosition('top', array('style' => 'block')); ?>
    </div>
<?php endif; ?>

<div class="rborder item-body">
    <div class="head-wrapper">
        <?php if ($this->checkPosition('image')) : ?>
            <div class="item-image <?php echo 'align-' . $align; ?>">
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

    <?php if ($this->checkPosition('price')) : ?>
        <div class="item-price jsCartModal"><?php echo $this->renderPosition('price'); ?></div>
    <?php endif; ?>

    <?php if ($this->checkPosition('properties')) : ?>
        <div class="item-properties">
            <h3><?php echo JText::_('JBZOO_QUICKVIEW_SPECIFICATION'); ?></h3>
            <table class="jbtable table zebra">
                <?php echo $this->renderPosition('properties', array('style' => 'jbtable')); ?>
            </table>
        </div>
    <?php endif; ?>

    <?php echo JBZOO_CLR; ?>

    <?php if ($this->checkPosition('bottom')) : ?>
        <div class="item-bottom">
            <?php echo $this->renderPosition('bottom', array('style' => 'block')); ?>
        </div>
    <?php endif; ?>

    <?php if ($this->checkPosition('related')) : ?>
        <div class="item-related">
            <?php echo $this->renderPosition('related', array('style' => 'block')); ?>
        </div>
    <?php endif; ?>
</div>

