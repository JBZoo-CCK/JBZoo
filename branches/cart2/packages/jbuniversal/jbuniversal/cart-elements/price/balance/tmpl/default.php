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
$list = array(
    '0'  => JText::_('JBZOO_NO'),
    '-1' => JText::_('JBZOO_YES'),
);

$mode    = (int)$params->get('config')->get('balance_mode', 0);
$variant = (int)$params->get('basic', 0) ? '' : '-variant';
$attr    = array(
    'class'       => 'balance' . $variant . '-input',
    'size'        => '60',
    'maxlength'   => '255',
    'placeholder' => 'balance'
);

?>

<div class="balance<?php echo $variant; ?>">
    <?php
    if ($mode) {
        echo $jbhtml->text($this->getName('_balance'), $this->getValue('_balance', 0), $attr);
    } else {
        echo $jbhtml->radio($list, $this->getName('_balance'), $attr, $this->getValue('_balance', 0));
    }
    ?>
</div>


