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
$jbhtml = $this->app->jbhtml;
$variant = (int)$params->get('basic', 0) ? '' : '-variant';

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

$inputAttr = array(
    'class'       => 'balance' . $variant . '-input jsBalanceInput',
    'size'        => '60',
    'maxlength'   => '255',
    'placeholder' => JText::_('JBZOO_JBPRICE_VARIATION_BALANCE')
);

$selected = null;
if ($this->getValue('_balance') > 0) {
    $selected = 1;
}

?>

<div class="balance<?php echo $variant; ?>" id="<?php echo $unique; ?>">

    <?php
    echo $jbhtml->radio($list, $this->getControlName('_balance'), $attr, $this->getValue('_balance', -1));
    ?>
    <div class="balance-custom">
        <?php
        echo $jbhtml->radio($text, $this->getControlName('_balance'), $radioAttr, $selected);
        echo $jbhtml->text($this->getControlName('_balance'), $this->getValue('_balance'), $jbhtml->buildAttrs($inputAttr));
        ?>
    </div>

</div>


<script type="text/javascript">
    (function ($) {
        $('#<?php echo $unique; ?>').JBZooPriceAdvanceBalanceHelper();
    })(jQuery)
</script>


