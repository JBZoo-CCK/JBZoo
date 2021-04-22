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

$uiqueId    = $this->app->jbstring->getId('sdek-');
$isPvz      = ($tariff == 136) ? true : false;
$isEmpty    = (empty($tariff)) ? true : false;

?>

<div id="<?php echo $uiqueId; ?>" class="sdek">
    <div class="sdek__sender">
        <p><?php echo JText::_('JBZOO_ELEMENT_SHIPPING_SDEK_FROM'); ?>: <?php echo $this->config->get('from')['city-name']; ?></p>
    </div>

    <div class="sdek__result">
        <div class="sdek__result-city" style="display: <?php echo $isEmpty ? 'none' : 'block'; ?>">
            <p><?php echo JText::_('JBZOO_ELEMENT_SHIPPING_SDEK_TO'); ?>: <span class="sdek__result-address jsSdekResultCity"><?php echo $this->get('to')['city-name']; ?></span></p>
        </div>

        <div class="sdek__result-pvz" style="display: <?php echo (!$isPvz || $isEmpty) ? 'none' : 'block'; ?>">
            <p><?php echo JText::_('JBZOO_ELEMENT_SHIPPING_SDEK_TYPE_1'); ?>: <span class="sdek__result-address jsSdekPvzAddress"><?php echo $address; ?></span></p>
        </div>

        <div class="sdek__result-courier" style="display: <?php echo ($isPvz || $isEmpty) ? 'none' : 'block'; ?>">
            <p><?php echo JText::_('JBZOO_ELEMENT_SHIPPING_SDEK_TYPE_2'); ?></p>
            <p class="sdek__result-address">
                <?php echo $this->app->html->_('control.text', $this->getControlName('address'), $address, 'class="jsSdekAddress" placeholder="'.JText::_('JBZOO_ELEMENT_SHIPPING_SDEK_INPUT_PLACEHOLDER').'" autocomplete="off"'); ?>
            </p>
        </div>
    </div>

    <div class="sdek__client">
        <p>
            <a href="#sdek-map" class="jsGetSdek"><?php echo $isEmpty ? JText::_('JBZOO_ELEMENT_SHIPPING_SDEK_SELECT') : JText::_('JBZOO_ELEMENT_SHIPPING_SDEK_CHANGE'); ?></a>
        </p>   
    </div>

    <div id="sdek-pvz" style="width:100%; height:500px; display: none;"></div>

    <?php echo $this->app->html->_('control.input', 'hidden', $this->getControlName('tariff'), $tariff, 'class="jsSdekTariff"'); ?>
    <?php echo $this->app->html->_('control.input', 'hidden', $this->getControlName('pvz'), $pvz, 'class="jsSdekPvz"'); ?>
    <?php echo $this->app->html->_('control.input', 'hidden', $this->getControlName('to').'[city-name]', $to ? $to['city-name'] : '', 'class="jsSdekCityToName"'); ?>
    <?php echo $this->app->html->_('control.input', 'hidden', $this->getControlName('to').'[city-id]', $to ? $to['city-id'] : '', 'class="jsSdekCityToId"'); ?>
    <?php echo $this->app->html->_('control.input', 'hidden', $this->getControlName('value'), $value, 'class="jsSdekValue"'); ?>
</div>

<?php echo $this->app->jbassets->widget('#'.$uiqueId, 'JBZooShippingTypeSdek', array(
    'path'          => $this->app->path->relative($this->app->path->path('cart-elements:shipping/sdek/assets/')),
    'servicePath'   => $this->_getAjaxSdekServiceUrl(),
    'templatePath'  => $this->_getAjaxSdekTemplateUrl(),
    'city'          => $this->_getDefaultCityName(),
    'key'           => $this->config->get('key'),
    'free'          => $this->config->get('limit_for_free'),
    'free_courier'  => $this->config->get('free_courier', 0),
    'rate'          => $rate,
    'goods'         => $goods,
), true); ?>