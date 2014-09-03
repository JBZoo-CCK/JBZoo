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
$countries = $this->_getLocations('countries');
$cities = $this->_getLocations('cities');

?>

<div class="empost-countries">
    <?php echo $jbhtml->select($countries, $this->getControlName('to'), array('id' => 'shippingcountryto'), null, 'shippingcountryto'); ?>
</div>
<div class="empost-cities">
    <?php echo $jbhtml->select($cities, $this->getControlName('to')); ?>
</div>