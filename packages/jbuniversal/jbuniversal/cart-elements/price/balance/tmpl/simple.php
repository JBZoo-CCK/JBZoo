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

?>
<div class="jbprice-balance">
    <span class="balance">
        <?php if ($this->getBasic('_balance') == 0) : ?>
            <?php echo $textNo; ?>
        <?php elseif ($this->getBasic('_balance') == -2) : ?>
            <?php echo $textOrder; ?>
        <?php
        else : ?>
            <?php echo $textYes; ?>
        <?php endif; ?>
    </span>
</div>