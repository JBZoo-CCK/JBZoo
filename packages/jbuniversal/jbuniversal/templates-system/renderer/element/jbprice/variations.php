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

$name = $params['name'];
$class = 'simple-param';
if ($element->isCore()) {
    $class = 'core-param';
}

$name = JText::_($name);

?>
<div class="variant-<?php echo strtolower($element->getElementType()); ?>-wrap <?php echo $class; ?> variant-param">

    <strong class="hasTip row-field label"
            title="<?php echo $name; ?>">
        <?php echo ucfirst($name); ?>
    </strong>

    <span class="attention jsJBPriceAttention"></span>

    <div class="field">
        <?php echo $element->edit($params); ?>
    </div>

</div>
