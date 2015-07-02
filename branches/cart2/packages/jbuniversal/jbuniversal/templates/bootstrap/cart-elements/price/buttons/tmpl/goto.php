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


$jbtemplate = $this->app->zoo->getApplication()->jbtemplate;
?>

<span class="jsPriceButton jsGoTo btn btn-default jbprice-buttons-goto">
    <?php echo $jbtemplate->icon('share', array('type' => 'white')); ?>
    <?php echo JText::_($params->get('goto_label', 'JBZOO_ELEMENT_PRICE_BUTTONS_GOTO_LABEL_DEFAULT')); ?>
</span>
