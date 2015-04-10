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

$elId = $this->app->jbstring->getId('');
$name = $element->config->get('type');
$lang = JText::_($name);
$class = 'simple-param';

if ($element->isCore()) {
    $name  = strtoupper($params['type']);
    $lang  = JText::_('JBZOO_JBPRICE_VARIATION_' . $name);
    $class = 'core-param';
}

?>
<div class="variant-<?php echo strtolower($name); ?>-wrap <?php echo $class; ?> variant-param">
    <strong class="hasTip row-field label"
            title="<?php echo $lang; ?>">
        <?php echo JString::ucfirst($lang); ?>
    </strong>
    <span class="attention jsJBpriceAttention"></span>

    <div class="field">
        <?php echo $element->edit($params); ?>
    </div>

</div>
