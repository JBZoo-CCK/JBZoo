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

$unique  = $this->htmlId(true);
$default = $params->get('default', 1);
$name    = $this->getRenderName('value');
?>

<div class="jbprice-quantity">
    <?php echo $this->_jbhtml->quantity($default, $params, $unique, $name, true); ?>
</div>
