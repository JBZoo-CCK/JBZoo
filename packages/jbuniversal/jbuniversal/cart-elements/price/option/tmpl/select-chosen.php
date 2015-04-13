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

$this->js('jbassets:js/libs/chosen.min.js');
$this->css('jbassets:css/libs/chosen.css'); ?>

<span class="jbprice-option-select-chosen">
    <?php echo $this->_jbhtml->selectChosen($data, $this->getRenderName('value'), null, $this->getValue(), $this->htmlId(true)); ?>
</span>
