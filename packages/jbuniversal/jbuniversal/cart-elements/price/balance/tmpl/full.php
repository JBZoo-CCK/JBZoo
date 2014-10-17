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

$balance = $this->getValue('value');

?>
<div class="jbprice-balance jsJBPriceBalance">
    <span class="balance">
        <?php
        if ($balance > 0) {
            echo '<span class="available">' . JText::_('JBZOO_JBPRICE_BALANCE_TEXT') . ': ' . $balance . '</span>';

        } else if ($balance > 0) {
            echo $textYes;

        } else if ($balance == -1) {
            echo $textYes;

        } else if ($balance == -2) {
            echo $textOrder;

        } else if ($balance == 0) {
            echo $textNo;
        }
        ?>
</span>
</div>