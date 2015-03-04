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
defined('_JEXEC') or die('Restricted access');

$this->app->jbassets->fancybox(); ?>

<div class="jbprice-buttons jsPriceButtons <?php echo $inCart; ?>">
    <span class="jsAddToCartModal uk-button uk-button-success add-button"
          title="<?php echo JText::_('JBZOO_JBPRICE_ADD_TO_CART_MODAL'); ?>"><?php echo $addLabel; ?></span>
</div>