<?php
/**
 * JBZoo App is universal Joomla CCK, application for YooTheme Zoo component
 * @package     jbzoo
 * @version     2.x Pro
 * @author      JBZoo App http://jbzoo.com
 * @copyright   Copyright (C) JBZoo.com,  All rights reserved.
 * @license     http://jbzoo.com/license-pro.php JBZoo Licence
 * @coder       Denis Smetannikov <denis@jbzoo.com>
 */

// no direct access
defined('_JEXEC') or die('Restricted access');


$jbform    = $this->app->jbform;
$formAttrs = $jbform->getDefaultFormOptions();

JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');
$this->app->jbtoolbar->save();

?>
<div class="uk-grid">
    <div id="sidebar" class="uk-width-1-6">
        <?php echo $this->partial('navigation'); ?>
    </div>

    <div class="uk-width-4-6">

        <h2><?php echo JText::_('JBZOO_CART_CONFIG_TITLE'); ?></h2>

        <?php echo $this->partial('cartdesc'); ?>

        <ul class="uk-tab" data-uk-tab="{connect:'#tab-comments'}">
            <li class="uk-active">
                <a href="javascript:void(0);"><?php echo JText::_('JBZOO_CART_CONFIG_CART_GENERAL'); ?></a>
            </li>
            <li><a href="javascript:void(0);"><?php echo JText::_('JBZOO_CART_CONFIG_CART_TEMPLATE'); ?></a></li>
            <li><a href="javascript:void(0);"><?php echo JText::_('JBZOO_CART_CONFIG_SHOP_ABOUT'); ?></a></li>
            <li><a href="javascript:void(0);"><?php echo JText::_('JBZOO_CART_CONFIG_SHOP_SHIPPING'); ?></a></li>
            <li><a href="javascript:void(0);"><?php echo JText::_('JBZOO_CART_CONFIG_SHOP_PAYMENT'); ?></a></li>
        </ul>

        <form <?php echo $this->app->jbhtml->buildAttrs($formAttrs);?>>
            <ul id="tab-comments" class="uk-switcher uk-margin">
                <li class="uk-active"><?php echo $jbform->renderFields('config_cart_general', $this->configData); ?></li>
                <li><?php echo $jbform->renderFields('config_cart_template', $this->configData); ?></li>
                <li><?php echo $jbform->renderFields('config_cart_shop', $this->configData); ?></li>
                <li><?php echo $jbform->renderFields('config_cart_shipping', $this->configData); ?></li>
                <li><?php echo $jbform->renderFields('config_cart_payment', $this->configData); ?></li>
            </ul>
        </form>

        <?php echo $this->partial('footer'); ?>
    </div>

    <div id="right-sidebar" class="uk-width-1-6">
        <?php echo $this->partial('right'); ?>
    </div>
</div>
