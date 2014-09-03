<?php
/**
 * JBZoo App is universal Joomla CCK, application for YooTheme Zoo component
 *
 * @package     jbzoo
 * @version     2.x Pro
 * @author      JBZoo App http://jbzoo.com
 * @copyright   Copyright (C) JBZoo.com,  All rights reserved.
 * @license     http://jbzoo.com/license-pro.php JBZoo Licence
 * @coder       Alexander Oganov <t_tapak@yahoo.com>
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

$jbhtml = $this->app->jbhtml;
$regions = $this->getRegions();
$cities = $this->getCities();

$deliveryType = array(
    '3' => JText::_('JBZOO_DELIVERY_NEWPOST_TO_DOORS'),
    '4' => JText::_('JBZOO_DELIVERY_NEWPOST_TO_WAREHOUSE')
);

$cityAttrs = array(
    'placeholder' => JText::_('City')
);

?>

<div class="newpost-deliverytype">
    <?php echo $jbhtml->select($deliveryType, $this->getControlName('deliverytype_id')); ?>
</div>

<div class="newpost-regions jsNewPostRegions">
    <?php echo $jbhtml->select($regions, $this->getControlName('regions')); ?>
</div>

<div class="newpost-cities jsNewPostSenderCity">
    <?php echo $jbhtml->select($cities, $this->getControlName('recipientcity')); ?>
</div>

<div class="newpost-to-warehouse jsAreaWarehouse">
    <div class="newpost-warehouse jsNewPostWareehouse">
        <?php echo $jbhtml->select($this->getWarehouses(), $this->getControlName('street')); ?>
    </div>
</div>

<script>
    jQuery(document).ready(function ($) {
        $('.jbzoo .shipping-list').JBCartShipping();
    })
</script>