<?php
/**
 * JBZoo App is universal Joomla CCK, application for YooTheme Zoo component
 * @package     jbzoo
 * @version     2.x Pro
 * @author      JBZoo App http://jbzoo.com
 * @copyright   Copyright (C) JBZoo.com,  All rights reserved.
 * @license     http://jbzoo.com/license-pro.php JBZoo Licence
 * @coder       Alexander Oganov <t_tapak@yahoo.com>
 */

// no direct access
defined('_JEXEC') or die('Restricted access');


if ($this->checkPosition(JBCart::DEFAULT_POSITION)) {
    $this->app->jbassets->less('jbassets:less/cart/validator.less');

    echo '<div class="jbcart-validator">' .
             $this->renderPosition(JBCart::DEFAULT_POSITION, array('style' => 'order.validator')) .
         '</div>';
}
