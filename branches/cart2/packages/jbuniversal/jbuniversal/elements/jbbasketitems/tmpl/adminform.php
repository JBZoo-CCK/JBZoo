<?php
/**
 * JBZoo App is universal Joomla CCK, application for YooTheme Zoo component
 *
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
<div class="jbzoo jsBasketItems basketItems">

    <?php
    echo $this->app->jbhtml->select($this->_getStatusList(), $this->getControlName('order_info_status'), array('class' => 'jsOrderStatus orderStatus'), $status);

    echo $this->app->html->_('control.textarea', $this->getControlName('order_info_description'), $description,
        'placeholder="' . JText::_('JBZOO_JBBASKETITEMS_ORDER_DESCRIPTION') . '" class="orderDescription" cols="60" rows="10"');

    echo $this->app->jbhtml->hidden($this->getControlName('order_info_admin'), 1);

    ?>

</div>
