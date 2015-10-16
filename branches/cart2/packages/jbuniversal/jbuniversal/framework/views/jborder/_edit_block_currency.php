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


$curList = $order->getCurrencyList();
if (count($curList) <= 2) {
    return false;
}

?>
<div class="uk-panel uk-panel-box basic-info currency-info">
    <h3 class="uk-panel-title"><?php echo JText::_('JBZOO_ORDER_CURRENCY_TITLE'); ?></h3>
    <p><?php echo JText::_('JBZOO_ORDER_CURRENCY_DESCRIPTION'); ?></p>

    <?php

    $this->app->jbassets->addVar('currencyList', $curList);

    echo $this->app->jbhtml->currencyToggle(JBCartValue::DEFAULT_CODE, $curList, array(
        'target'      => '.jbzoo .uk-grid',
        'showDefault' => true,
    ));
    ?>

</div>