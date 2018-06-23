<?php
/**
 * JBZoo Application
 *
 * This file is part of the JBZoo CCK package.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package    Application
 * @license    GPL-2.0
 * @copyright  Copyright (C) JBZoo.com, All rights reserved.
 * @link       https://github.com/JBZoo/JBZoo
 * @author     Denis Smetannikov <denis@jbzoo.com>
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
