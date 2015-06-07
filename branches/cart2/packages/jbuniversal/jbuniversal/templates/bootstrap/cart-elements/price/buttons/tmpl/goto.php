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

?>

<span class="jsPriceButton jsGoTo uk-button jbprice-buttons-goto">
    <i class="uk-icon-level-up"></i>
    <?php echo JText::_($params->get('goto_label', 'JBZOO_ELEMENT_PRICE_BUTTONS_GOTO_LABEL_DEFAULT')); ?>
</span>
