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
defined('_JEXEC') or die('Restricted access'); ?>
<div class="jbprice-balance jsJBPriceBalance">
    <span class="balance">
        <?php if (!$useStock) {
            echo $textYes;

        } elseif ($balance == 0) {
            echo $textNo;

        } elseif ($balance == -2) {
            echo $textOrder;

        } else {
            echo $textYes;
        } ?>
    </span>
</div>