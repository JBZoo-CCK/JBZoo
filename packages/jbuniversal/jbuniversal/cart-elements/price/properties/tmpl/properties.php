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

<?php if ($width): ?>
    <span class="jbprice-properties-width">
        <?php echo JText::sprintf('JBZOO_ELEMENT_PRICE_PROPERTIES_WIDTH_UNIT', $width); ?>
    </span>
<?php endif; ?>

<?php if ($height): ?>
    <span class="jbprice-properties-height">
        <?php echo JText::sprintf('JBZOO_ELEMENT_PRICE_PROPERTIES_HEIGHT_UNIT', $height); ?>
    </span>
<?php endif; ?>

<?php if ($length): ?>
    <span class="jbprice-properties-length">
        <?php echo JText::sprintf('JBZOO_ELEMENT_PRICE_PROPERTIES_LENGTH_UNIT', $length); ?>
    </span>
<?php endif; ?>
