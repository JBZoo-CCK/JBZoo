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


$view = $this->getView();
$this->app->jbassets->basket();
$this->app->jbassets->initJBPrice();
$actionUrl = $this->app->jbrouter->cartOrderCreate($view->application->id, null);
$this->app->jbassets->chosen();

if (count($view->items)) : ?>

    <form action="<?php echo $actionUrl; ?>" method="post" name="jbcartForm" class="jbzoo-app-basket"
          accept-charset="utf-8"
          enctype="multipart/form-data">

        <?php echo $this->partial('basket', 'table'); ?>

        <div class="create-order">

            <h3 class="title-name">Оформление заказа</h3>

            <?php echo $this->partial('basket', 'form');

            echo $this->partial('basket', 'shipping');

            echo $this->partial('basket', 'shippingfield');

            echo $this->partial('basket', 'payment'); ?>

            <div class="jbzoo-clear jbzoo-order-submit">
                <input type="submit" value="Оформить заказ" class="jbbutton-base jbbutton-green jbbutton-big" />

                <input type="hidden" name="option" value="com_zoo" />
                <input type="hidden" name="controller" value="basket" />
                <input type="hidden" name="task" value="index" />
                <input type="hidden" name="Itemid" value="<?php echo $view->Itemid; ?>" />
                <?php echo $this->app->html->_('form.token'); ?>
            </div>
        </div>
    </form>

<?php endif;