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

$view = $this->getView();


?>

<?php if (count($view->items) == 0) : ?>
    <p class="jbcart-empty-message"><?php echo JText::_('JBZOO_CART_ITEMS_NOT_FOUND'); ?></p>

<?php else:

    $this->app->jbassets->heightFix('.uk-panel');

    $this->app->jbassets->less(array('jbassets:less/general/cart.less'));

    $this->app->jbassets->widget('.jbzoo .jsJBZooCart', 'JBZoo.Cart', array(
        'text_remove_all'  => JText::_('JBZOO_CART_CLEANUP'),
        'text_remove_item' => JText::_('JBZOO_CART_REMOVE_ITEM'),
        'url_shipping'     => $this->app->jbrouter->basketShipping(),
        'url_quantity'     => $this->app->jbrouter->basketQuantity(),
        'url_delete'       => $this->app->jbrouter->basketDelete(),
        'url_clear'        => $this->app->jbrouter->basketClear(),
        'items'            => $view->order->getItems(false),
    ));

    $formAttrs = array(
        'action'         => $this->app->jbrouter->cartOrderCreate(),
        'method'         => 'post',
        'name'           => 'jbcartForm',
        'accept-charset' => 'utf-8',
        //'target'         => '_blank',
        'enctype'        => 'multipart/form-data',
        'class'          => array(
            'jbcart',
            'jsJBZooCart',
            'uk-form',
        ),
    );

    ?>
    <form <?php echo $this->app->jbhtml->buildAttrs($formAttrs); ?>>

        <?php echo $this->partial('basket', 'validators'); ?>
        <?php echo $this->partial('basket', 'table'); ?>
        <?php echo $this->partial('basket', 'form'); ?>
        <?php echo $this->partial('basket', 'shipping'); ?>
        <?php echo $this->partial('basket', 'payment'); ?>
        <?php echo $this->partial('basket', 'buttons'); ?>
        <?php echo $this->partial('basket', 'mobile_tools'); ?>

        <?php
        // system fields
        echo $this->app->jbhtml->hiddens(array(
                'option'     => 'com_zoo',
                'controller' => 'basket',
                'task'       => 'index',
                'Itemid'     => $view->Itemid,
            ))
            . $this->app->html->_('form.token');
        ?>
    </form>

<?php endif;
