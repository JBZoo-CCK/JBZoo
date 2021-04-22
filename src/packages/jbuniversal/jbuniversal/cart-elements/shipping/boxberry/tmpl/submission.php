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

$uiqueId = $this->app->jbstring->getId('boxberry-');

$isSelected = ($price && $address && $pvz) ? true : false;

?>

<div id="<?php echo $uiqueId; ?>" class="boxberry">
    <div class="boxberry__sender">
        <p><?php echo JText::_('JBZOO_ELEMENT_SHIPPING_BOXBERRY_FROM'); ?>: <?php echo $this->config->get('city'); ?></p>
    </div>

    <div class="boxberry__result" style="display: <?php echo !$isSelected ? 'none' : 'block'; ?>">
        <p><?php echo JText::_('JBZOO_ELEMENT_SHIPPING_BOXBERRY_TYPE_1'); ?>: <span class="boxberry__result-address jsBoxberryPvzAddress">ID: <?php echo $pvz; ?>, <?php echo $address; ?></span></p>
    </div>

    <div class="boxberry__client">
        <p>
            <a href="#boxberry-map" class="jsGetBoxberry"><?php echo !$isSelected ? JText::_('JBZOO_ELEMENT_SHIPPING_BOXBERRY_SELECT') : JText::_('JBZOO_ELEMENT_SHIPPING_BOXBERRY_CHANGE'); ?></a>
        </p>   
    </div>

    <?php echo $this->app->html->_('control.input', 'hidden', $this->getControlName('pvz'), $pvz, 'class="jsBoxberryPzv"'); ?>
    <?php echo $this->app->html->_('control.input', 'hidden', $this->getControlName('address'), $address, 'class="jsBoxberryAddress"'); ?>
    <?php echo $this->app->html->_('control.input', 'hidden', $this->getControlName('value'), $price, 'class="jsBoxberryValue"'); ?>
</div>

<?php echo $this->app->jbassets->widget('#'.$uiqueId, 'JBZooShippingTypeBoxberry', array(
    'api_token'         => $this->config->get('api_token'),
    'sum'               => $sum,
    'city'              => $this->config->get('city'),
    'free'              => $this->config->get('limit_for_free'),
    'rate'              => $this->config->get('rate'),
    'weight'            => (int) $weight,
    'box_lenght'        => (int) $this->config->get('box_length'),
    'box_height'        => (int) $this->config->get('box_height'),
    'box_width'         => (int) $this->config->get('box_width'),
    'yandex_map_key'    => $this->config->get('yandex_map_key'),
), true); ?>