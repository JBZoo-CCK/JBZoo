<?php
/**
 * JBZoo App is universal Joomla CCK, application for YooTheme Zoo component
 * @package     jbzoo
 * @version     2.x Pro
 * @author      JBZoo App http://jbzoo.com
 * @copyright   Copyright (C) JBZoo.com,  All rights reserved.
 * @license     http://jbzoo.com/license-pro.php JBZoo Licence
 * @coder       Denis Smetannikov <denis@jbzoo.com>
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

?>

<div class="uk-text-center jbform-actions uk-clearfix">

    <input type="submit" name="create" value="<?php echo JText::_('JBZOO_CART_MOBILE_SUBMIT'); ?>"
           class="uk-button uk-button-large uk-button-success" />

    <?php if ($view->payment) : ?>
        <input type="submit" name="create-pay" value="<?php echo JText::_('JBZOO_CART_MOBILE_SUBMIT_AND_PAY'); ?>"
               class="uk-button uk-button-large uk-button-success" />
    <?php endif; ?>

</div>
