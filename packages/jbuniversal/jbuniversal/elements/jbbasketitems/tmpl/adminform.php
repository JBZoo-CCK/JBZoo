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

?>
<div class="jbzoo jsBasketItems basketItems">

    <?php
    echo $this->app->jbhtml->select($this->_getStatusList(), $this->getControlName('order_info_status'), array('class' => 'jsOrderStatus orderStatus'), $status);

    echo $this->app->html->_('control.textarea', $this->getControlName('order_info_description'), $description,
        'placeholder="' . JText::_('JBZOO_JBBASKETITEMS_ORDER_DESCRIPTION') . '" class="orderDescription" cols="60" rows="10"');

    echo $this->app->jbhtml->hidden($this->getControlName('order_info_admin'), 1);

    ?>

</div>
