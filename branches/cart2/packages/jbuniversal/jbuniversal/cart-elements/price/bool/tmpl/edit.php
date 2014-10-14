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

$data = array(
    ''  => JText::_('JBZOO_CORE_PRICE_OPTIONS_DEFAULT'),
    '0' => JText::_('JBZOO_NO'),
    '1' => JText::_('JBZOO_YES')
);

?>

<div class="controls">
    <?php echo $this->app->jbhtml->radio($data, $this->getControlName('value'), NULL, $this->getValue('value')); ?>
</div>




