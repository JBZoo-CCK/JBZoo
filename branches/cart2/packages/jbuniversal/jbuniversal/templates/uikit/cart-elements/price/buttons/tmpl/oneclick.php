<?php
/**
 * JBZoo App is universal Joomla CCK, application for YooTheme Zoo component
 * @package     jbzoo
 * @version     2.x Pro
 * @author      JBZoo App http://jbzoo.com
 * @copyright   Copyright (C) JBZoo.com,  All rights reserved.
 * @license     http://jbzoo.com/license-pro.php JBZoo Licence
 * @coder       Alexander Oganov <t_tapak@yahoo.com>
 * @coder       Sergey Kalistratov <kalistratov.s.m@gmail.com>
 */

// no direct access
defined('_JEXEC') or die('Restricted access'); ?>

<div class="jbprice-buttons jsPriceButtons <?php echo $inCart; ?>">
    <span class="jsAddToCartGoto uk-button uk-button-success add-button"
          title="<?php echo $addLabel; ?>"><?php echo $addLabel; ?></span>

    <span class="jsRemoveFromCart uk-button uk-button-danger uk-button-small remove-button remove-button"
          title="<?php echo $removeLabel; ?>"><?php echo $removeLabel; ?></span>
</div>
