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

$unique = $this->app->jbstring->getId('balance-custom-');
$html = $this->app->jbhtml;
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
    'class' => 'balance' . $variant . '-input',
    'id'    => $this->app->jbstring->getId()
);

$radioAttr = array(
    'class' => 'balance' . $variant . '-input jsBalanceRadio',
    'id'    => $this->app->jbstring->getId()
);

$inputAttr = $html->buildAttrs(array(
    'class'       => 'balance' . $variant . '-input jsBalanceInput',
    'size'        => '60',
    'maxlength'   => '255',
    'placeholder' => JText::_('JBZOO_JBPRICE_VARIATION_BALANCE'),
    'style'       => 'width:100px; text-align:left;'
));

$selected = NULL;
$value = $this->getValue('value', -1);

if ($value > 0) {
    $selected = 1;
}

?>

<div class="balance<?php echo $variant; ?>" id="<?php echo $unique; ?>">

    <?php echo $html->radio($list, $this->getControlName('value'), $attr, $value, $selected); ?>

    <div class="balance-custom">
        <?php
        echo $html->radio($text, $this->getControlName('value'), $radioAttr, $selected);
        echo $html->text($this->getControlName('value'), $value, $inputAttr);
        ?>
    </div>

</div>

<script type="text/javascript">
    (function ($) {
        $('#<?php echo $unique; ?>').JBZooPriceAdvanceBalanceHelper();
    })(jQuery)
</script>


