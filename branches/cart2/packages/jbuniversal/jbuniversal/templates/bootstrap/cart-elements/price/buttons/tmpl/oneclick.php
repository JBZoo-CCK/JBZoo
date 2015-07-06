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


$bootstrap = $this->app->jbbootstrap;
?>

<span class="jsAddToCart jsGoTo btn btn-success jbprice-buttons-oneclick">
    <?php echo $bootstrap->icon('share', array('type' => 'white')); ?>
    <?php echo JText::_($params->get('oneclick_label', 'JBZOO_ELEMENT_PRICE_BUTTONS_ONECLICK_LABEL_DEFAULT')); ?>
</span>
