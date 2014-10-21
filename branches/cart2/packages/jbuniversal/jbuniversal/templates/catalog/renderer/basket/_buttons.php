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

<div class="jbzoo-clear jbzoo-order-submit">

    <input type="submit" name="create" value="Создать заказ" class="jbbutton-base jbbutton-green jbbutton-big" />

    <?php if ($view->payment) : ?>
        <input type="submit" name="create-pay" value="Заказать и оплатить"
               class="jbbutton-base jbbutton-green jbbutton-big" />
    <?php endif; ?>

</div>
