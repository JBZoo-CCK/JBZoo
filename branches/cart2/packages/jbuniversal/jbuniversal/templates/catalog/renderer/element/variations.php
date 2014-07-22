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
$name = $element->config->get('name');
$lang = JText::_($name);

if ($element->isCore()) {
    $name = strtoupper($params['type']);
    $lang = JText::_('JBZOO_JBPRICE_VARIATION_' . $name);
}

?>
<div class="variant-<?php echo strtolower($name); ?>-wrap">
    <label for="<?php echo $elId . '-' . $name; ?>" class="hasTip row-field"
           title="<?php echo $lang; ?>">
        <?php echo $lang; ?>
    </label>
    <?php echo $element->edit(); ?>

</div>
