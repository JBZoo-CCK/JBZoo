<?php
/**
 * JBZoo App is universal Joomla CCK, application for YooTheme Zoo component
 * @package     jbzoo
 * @version     2.x Pro
 * @author      JBZoo App http://jbzoo.com
 * @copyright   Copyright (C) JBZoo.com,  All rights reserved.
 * @license     http://jbzoo.com/license-pro.php JBZoo Licence
 * @coder       Alexander Oganov <t_tapak@yahoo.com>
 */

// no direct access
defined('_JEXEC') or die('Restricted access');


$jbhtml  = $this->app->jbhtml;
$uiqueId = $this->app->jbstring->getId('newpost-');

?>

<div class="newpost-sender-city">
    <p>
        <strong><?php echo JText::_('JBZOO_ELEMENT_SHIPPING_NEWPOST_SENDER'); ?>:</strong>
        <?php echo $this->_getDefaultCity(); ?>
    </p>
</div>

<div id="<?php echo $uiqueId; ?>">
    <div class="newpost-deliveryType_id">
        <?php echo $jbhtml->select($this->_getTypeList(), $this->getControlName('deliveryType_id'),
            'class="jsDeliveryType"', $this->get('deliveryType_id', JBCartElementShippingNewPost::DEFAULT_TYPE)); ?>
    </div>

    <div class="newpost-region">
        <?php echo $jbhtml->select($this->_getRegionList(), $this->getControlName('region'),
            'class="jsRegion"', $this->get('region', JBCartElementShippingNewPost::DEFAULT_REGION)); ?>
    </div>

    <div class="newpost-recipientCity">
        <?php echo $jbhtml->select($this->_getCityList($this->get('region', JBCartElementShippingNewPost::DEFAULT_REGION)),
            $this->getControlName('recipientCity'), 'class="jsRecipientCity"',
            $this->get('recipientCity', JBCartElementShippingNewPost::DEFAULT_CITY)); ?>
    </div>

    <div class="newpost-warehouse jsWarehouseWrapper">
        <?php echo $jbhtml->select($this->_getWarehouseList($this->get('recipientCity')),
            $this->getControlName('warehouse'), 'class="jsWarehouse"', $this->get('warehouse')); ?>
    </div>

    <div class="newpost-doors jsDoorsWrapper">
        <?php
        echo $jbhtml->text(
            $this->getControlName('street'),
            $this->get('street'),
            array('placeholder' => JText::_('JBZOO_ELEMENT_SHIPPING_NEWPOST_STREET'))
        );
        echo $jbhtml->text(
            $this->getControlName('floor_count'),
            $this->get('floor_count'),
            array('placeholder' => JText::_('JBZOO_ELEMENT_SHIPPING_NEWPOST_FLOOR_COUNT'))
        );
        ?>
    </div>
</div>

<?php echo $this->app->jbassets->widget('#' . $uiqueId, 'JBZooShippingTypeNewpost', array(
    'type_doors'    => JBCartElementShippingNewPost::TYPE_DOORS,
    'type_ware'     => JBCartElementShippingNewPost::TYPE_WARE,
    'url_locations' => $this->_getAjaxLocationsUrl(),
), true); ?>

