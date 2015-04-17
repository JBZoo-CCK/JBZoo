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

if (!$this->app->jbenv->isSite()) {
    $this->app->jbassets->widget('#' . $unique, 'JBZooMedia');
}
?>

<div class="jsMedia jbprice-img-row-file" id="<?php echo $unique; ?>">
    <?php
    echo $this->_jbhtml->text($this->getControlName('value'), $value, array(
        'class'       => 'jsJBPriceImage jsMediaValue row-file',
        'placeholder' => JText::_('JBZOO_ELEMENT_PRICE_IMAGE_EDIT_PLACEHOLDER')
    )); ?>
</div>
