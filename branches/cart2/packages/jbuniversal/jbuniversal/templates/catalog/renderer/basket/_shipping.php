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

if (!empty($view->shipping))  : ?>

    <!--
        You can not delete the class!
        This base class is used in conjunction with js.
        If you want to change the layout, be sure to use this class.
    -->
    <div class="shipping-list">
        <?php echo $view->shippingRenderer->render(
            'shipping.default', array(
                'order' => $view->order
            )
        );?>
    </div>

<?php endif;

