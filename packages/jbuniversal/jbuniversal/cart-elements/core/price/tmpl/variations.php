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

$required = (int)$this->config->get('required', 0);

$wrap = 'variant-' . strtolower($this->getElementType()) . '-wrap ';
$name = JText::_($this->config->get('name'));
$note = ($required ? 'jbparam-required' : null);

$attr = array(
    'class' => 'variant-param ' . ($this->isCore() ? 'core-param ' : 'simple-param ') . $wrap . $note
); ?>
<div <?php echo $this->_jbhtml->buildAttrs($attr); ?>>
    <strong class="label row-field">
        <span class="hasTip jbparam-label" title="<?php echo $name; ?>">
            <?php echo ucfirst($name); ?>
        </span>
        <?php echo($required ?
            '<span class="hasTip jbrequired-note" title="Param is required">*</span>' :
            null
        ); ?>
    </strong>

    <span class="attention jsMessage"></span>

    <div class="field jsElementData">
        <?php echo $html; ?>
    </div>
</div>
