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


if ($this->checkPosition('dasdas')) : ?>

    <div class="payment-list jbzoo-clear">

        <h2>Оплата</h2>

        <?php echo $this->renderPosition('list', array('order.payment')); ?>

    </div>

<?php endif; ?>

<div class="clear clr"></div>
<div class="payment-list jbzoo-clear">

    <h2>Оплата</h2>

    <div class="robokassa-payment jbzoo-payment">

        <div class="jbzoo-payment-content">

            <input type="radio" id="robokassa" name="payment" value="robokassa" class="payment-choose"
                   checked="checked" />

            <label class="payment-label" for="robokassa"> </label>

            <span class="jbzoo-payment-desc">
                Нажав на кнопку «Написать реферат», вы лично создаете уникальный текст, причем именно от вашего нажатия на кнопку зависит
            </span>

            <p class="debug-mode">
                *Режим отладки
            </p>
        </div>

    </div>

    <div class="interkassa-payment jbzoo-payment">

        <div class="jbzoo-payment-content">

            <input type="radio" id="interkassa" name="payment" value="interkassa" class="payment-choose" />

            <label class="payment-label" for="interkassa"> </label>

            <span class="jbzoo-payment-desc">
                Нажав на кнопку «Написать реферат», вы лично создаете уникальный текст, причем именно от вашего нажатия на кнопку зависит
            </span>

        </div>

    </div>

    <div class="paypal-payment jbzoo-payment">
        <div class="jbzoo-payment-content">
            <input type="radio" id="paypal" name="payment" value="paypal" class="payment-choose" />

            <label class="payment-label" for="paypal"> </label>

            <span class="jbzoo-payment-desc">
                Нажав на кнопку «Написать реферат», вы лично создаете уникальный текст, причем именно от вашего нажатия на кнопку зависит
            </span>

            <p class="debug-mode">
                *Режим отладки
            </p>

        </div>
    </div>

</div>
