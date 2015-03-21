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

$unique  = $this->htmlId(true);
$variant = (int)$this->config->get('_variant') ? '' : '-variant';

$text = array(
    '1' => ''
);
$list = array(
    '0'  => JText::_('JBZOO_JBPRICE_BALANCE_NOT_AVAILABLE'),
    '-1' => JText::_('JBZOO_JBPRICE_BALANCE_UNLIMITED'),
    '-2' => JText::_('JBZOO_JBPRICE_BALANCE_UNDER_ORDER')
);
$attr = array(
    'class' => 'balance-' . $this->variant . '-input jsBalanceRadio',
);

$radioAttr = array(
    'class' => 'balance-' . $this->variant . '-input jsBalanceRadio',
);

$inputAttr = $this->_jbhtml->buildAttrs(array(
    'class'       => 'balance-' . $this->variant . '-input jsBalanceInput',
    'placeholder' => JText::_('JBZOO_JBPRICE_VARIATION_BALANCE'),
));

$selected = null;
$value    = $this->get('value', -1);

if ($value > 0) {
    $selected = 1;
    $value    = $this->app->jbvars->number($value);
} ?>
    <div class="balance balance-<?php echo $this->variant; ?> jsBalance" id="<?php echo $unique; ?>">
        <?php echo $this->_jbhtml->radio($list, $this->getControlName('value'), $attr, $value, $selected); ?>

        <div class="balance-custom">
            <?php echo
            $this->_jbhtml->radio($text, $this->getControlName('value'), $radioAttr, $selected),
            $this->_jbhtml->text($this->getControlName('value'), $value, $inputAttr); ?>
        </div>
    </div>

<?php echo $this->app->jbassets->widget('.jsBalance', 'JBZooPriceBalance', array(), true);
