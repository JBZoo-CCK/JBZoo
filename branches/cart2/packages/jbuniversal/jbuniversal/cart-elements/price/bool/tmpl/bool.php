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

$unique = $this->app->jbstring->getId('bool-');
$value = $this->getBasic($this->identifier);

$arr = array(
    '1' => JText::_('JBZOO_YES'),
    '0' => JText::_('JBZOO_NO')
);
if (!empty($arr[$value['value']])) {
    $value = $value['value'];
}

?>

<div class="jbprice-bool" id="<?php echo $unique; ?>">
    <span class="bool"><?php echo $value; ?></span>
</div>