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

$viewPost = array(
    ''   => '-None-',
    '23' => JText::_('JBZOO_DELIVERY_RUSSIANPOST_PARCEL'),
    '18' => JText::_('JBZOO_DELIVERY_RUSSIANPOST_CARD'),
    '13' => JText::_('JBZOO_DELIVERY_RUSSIANPOST_LETTER'),
    '26' => JText::_('JBZOO_DELIVERY_RUSSIANPOST_RICH_PARCEL'),
    '36' => JText::_('JBZOO_DELIVERY_RUSSIANPOST_RICH_PACKAGE'),
    '16' => JText::_('JBZOO_DELIVERY_RUSSIANPOST_RICH_LETTER')
);
$typePost = array(
    ''  => '-None-',
    '1' => JText::_('JBZOO_DELIVERY_RUSSIANPOST_GROUND'),
    '2' => JText::_('JBZOO_DELIVERY_RUSSIANPOST_AIR'),
    '3' => JText::_('JBZOO_DELIVERY_RUSSIANPOST_COMBINE'),
    '4' => JText::_('JBZOO_DELIVERY_RUSSIANPOST_FAST')
);
$zipAttrs = array(
    'placeholder' => JText::_('JBZOO_DELIVERY_RUSSIANPOST_ZIP'),
    'id' => 'shippingpostOfficeId'
);
?>

<div class="russianpost-viewpost">
    <?php echo $jbhtml->select($viewPost, $this->getControlName('viewpost')); ?>
</div>
<div class="russianpost-typepost">
    <?php echo $jbhtml->select($typePost, $this->getControlName('typepost')); ?>
</div>
<div class="russianpost-postOfficeId">
    <?php echo $jbhtml->text($this->getControlName('postofficeid'), null, $zipAttrs); ?>
</div>