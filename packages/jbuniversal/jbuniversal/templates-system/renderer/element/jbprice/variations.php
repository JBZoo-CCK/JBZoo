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

$wrap  = 'variant-' . strtolower($element->getElementType()) . '-wrap';
$attr  = array(
    'class' => 'variant-param ' . ($element->isCore() ? 'core-param ' : 'simple-param ') . $wrap
);
$name  = JText::_($params['name']);?>
<div <?php echo $this->app->jbhtml->buildAttrs($attr); ?>>

    <strong class="hasTip row-field label"
            title="<?php echo $name; ?>">
        <?php echo ucfirst($name); ?>
    </strong>

    <span class="attention jsMessage"></span>

    <div class="field">
        <?php echo $element->edit(); ?>
    </div>

</div>
