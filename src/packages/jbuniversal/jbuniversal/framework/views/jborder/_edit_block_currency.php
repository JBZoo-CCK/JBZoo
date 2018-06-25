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