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
defined('_JEXEC') or die('Restricted access'); ?>

<div class="jbprice-buttons jsPriceButtons <?php echo $inCart; ?>">

    <?php echo $add_html, $oneClick_html, $popup_html, $goto_html; ?>

    <span class="jsRemoveFromCart jsRemoveElement jbbutton small orange remove-button" title="<?php echo $removeLabel; ?>">
        <?php echo $removeLabel; ?>
    </span>

</div>
