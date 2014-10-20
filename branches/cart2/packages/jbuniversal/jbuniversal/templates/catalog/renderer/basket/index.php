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

?>

<?php if (count($view->items) == 0) : ?>

    <?php echo JText::_('JBZOO_CART_ITEMS_NOT_FOUND'); ?>

<?php else: ?>

    <form action="<?php echo $actionUrl; ?>" method="post" name="jbcartForm" class="jbzoo-app-basket"
          accept-charset="utf-8" enctype="multipart/form-data">

        <?php echo $this->partial('basket', 'table'); ?>

        <div class="create-order">

            <h3 class="title-name"><?php echo JText::_('JBZOO_CART_ITEMS_NOT_FOUND'); ?></h3>

            <?php echo $this->partial('basket', 'form'); ?>

            <?php echo $this->partial('basket', 'shipping'); ?>

            <?php echo $this->partial('basket', 'shippingfield'); ?>

            <?php echo $this->partial('basket', 'payment'); ?>

            <div class="jbzoo-clear jbzoo-order-submit">
                <input type="submit" name="create" value="Создать заказ"
                       class="jbbutton-base jbbutton-green jbbutton-big" />

                <input type="submit" name="create-pay" value="Заказать и оплатить"
                       class="jbbutton-base jbbutton-green jbbutton-big" />
            </div>

            <input type="hidden" name="option" value="com_zoo" />
            <input type="hidden" name="controller" value="basket" />
            <input type="hidden" name="task" value="index" />
            <input type="hidden" name="Itemid" value="<?php echo $view->Itemid; ?>" />
            <?php echo $this->app->html->_('form.token'); ?>
        </div>
    </form>

    <script>
        jQuery(function ($) {
            $('.jbzoo .jbzoo-app-basket select').chosen();
        });
    </script>
<?php endif;