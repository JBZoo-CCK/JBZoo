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

$unique   = $this->htmlId(true);
$value    = $this->app->jbvars->number($this->get('value', -1));
$selected = $value > 0 ? 1 : null;

?>

    <div class="balance balance-<?php echo $this->variant; ?> jsBalance" id="<?php echo $unique; ?>">
        <?php echo $this->_jbhtml->radio(
            $this->_getList(),
            $this->getControlName('value'),
            array('class' => 'balance-' . $this->variant . '-input jsBalanceRadio'),
            $value,
            $selected
        ); ?>

        <div class="balance-custom">
            <?php
            // radio button (just for tmpl)
            echo $this->_jbhtml->radio(
                array('1' => ''),
                $this->getControlName('value'),
                array('class' => 'balance-' . $this->variant . '-input jsBalanceRadio'),
                $selected
            );

            // input box
            echo $this->_jbhtml->text(
                $this->getControlName('value'),
                $value,
                array(
                    'class'       => 'balance-' . $this->variant . '-input jsBalanceInput',
                    'placeholder' => JText::_('JBZOO_ELEMENT_PRICE_BALANCE_EDIT_PLACEHOLDER'),
                )
            );
            ?>
        </div>
    </div>

<?php echo $this->app->jbassets->widget('.jsBalance', 'JBZooPriceBalance', array(), true);
