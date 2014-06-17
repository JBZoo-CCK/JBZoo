<?php
/**
 * JBZoo App is universal Joomla CCK, application for YooTheme Zoo component
 *
 * @package     jbzoo
 * @version     2.x Pro
 * @author      JBZoo App http://jbzoo.com
 * @copyright   Copyright (C) JBZoo.com,  All rights reserved.
 * @license     http://jbzoo.com/license-pro.php JBZoo Licence
 * @coder       Denis Smetannikov <denis@jbzoo.com>
 */

// no direct access
defined('_JEXEC') or die('Restricted access');


$isMarked = ($this->checkPosition("marked") && strtolower(trim($this->renderPosition("marked"))) == "yes");

?>

<div class="realty-row item_<?php echo $item->id; ?> <?php echo ($isMarked) ? 'marked-element' : ''; ?>">

    <div class="teaser-image">
        <div class="mark-vip"></div><?php echo $this->renderPosition('image'); ?></div>

    <div class="main-info">

        <?php echo $this->renderPosition('price'); ?>

        <div class="rooms clearfix">

            <div
                class="beds <?php if (!$this->renderPosition('baths')): ?>no-baths<?php endif; ?> <?php if (!$this->renderPosition('baths')): ?>no-beds<?php endif; ?>">
                <div class="value-beds marked-realty"><?php echo $this->renderPosition('beds'); ?></div>
                <div class="label-beds"><?php echo JText::_('JBZOO_TMPL_FLAT_PROPS_BEDS'); ?></div>
            </div>

            <?php if ($this->checkPosition('baths')): ?>
                <div class="baths">
                    <div class="value-baths marked-realty"><?php echo $this->renderPosition('baths'); ?></div>
                    <div class="label-baths"><?php echo JText::_('JBZOO_TMPL_FLAT_PROPS_BATHS'); ?></div>
                </div>
            <?php endif; ?>
        </div>

        <div class="realty-save">
            <?php echo $this->renderPosition('save'); ?>
        </div>
    </div>

    <div class="more-info">
        <div class="listing-date"><?php echo $this->renderPosition('date'); ?></div>

        <p class="address"><?php echo $this->renderPosition('address'); ?></p>

        <div class="left-info">
            <?php if ($this->checkPosition('year')): ?>
                <div>
                    <?php echo JText::_('JBZOO_TMPL_FLAT_PROPS_YEARBUILD'); ?>
                    <span><?php echo $this->renderPosition('year'); ?></span>
                </div>
            <?php endif; ?>

            <?php if ($this->checkPosition('sqft')): ?>
                <div>
                    <?php echo JText::_('JBZOO_TMPL_FLAT_PROPS_SQFT'); ?>
                    <span><?php echo $this->renderPosition('sqft'); ?></span>
                </div>
            <?php endif; ?>

            <?php if ($this->checkPosition('compare')): ?>
                <div class="realty-compare">
                    <?php echo $this->renderPosition('compare'); ?>
                </div>
            <?php endif; ?>
        </div>

        <div class="right-info">
            <?php if ($this->checkPosition('type')): ?>
                <div>
                    <?php echo JText::_('JBZOO_TMPL_FLAT_PROPS_TYPEBUILD'); ?>
                    <span><?php echo $this->renderPosition('type'); ?></span>
                </div>
            <?php endif; ?>

            <?php if ($this->checkPosition('additionally')): ?>
                <div>
                    <?php echo JText::_('JBZOO_TMPL_FLAT_PROPS_ADDITION'); ?>
                    <span><?php echo $this->renderPosition('additionally'); ?></span>
                </div>
            <?php endif; ?>

            <?php if ($this->checkPosition('agent')): ?>
                <div class="agent-info">
                    <?php echo JText::_('JBZOO_TMPL_FLAT_PROPS_LISTING'); ?><?php echo $this->renderPosition('agent'); ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
