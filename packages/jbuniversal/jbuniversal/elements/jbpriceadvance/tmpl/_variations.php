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
$zoo    = $this->app;
$jbhtml = $this->app->jbhtml;

foreach ($variations as $rowKey => $row) : ?>

    <fieldset class="jbpriceadv-variation-row">

    <span class="jbremove"></span>

    <span class="variation-label">
        <?php echo JText::_('JBZOO_JBPRICE_VARIATION_ROW'); ?>
        #<span class="list-num"><?php echo $rowKey + 1; ?></span>
    </span>

    <?php
    //Render sku input
    echo $this->_renderRow('sku', $row['sku']);

    //Render value input
    echo $this->_renderRow('value', $row['value']);

    //Render currency list
    $currencyList = $zoo->jbarray->unshiftAssoc($currencyList, '%', '%');
    echo $jbhtml->select($currencyList, $this->getRowControlName('currency'), 'class="row-currency"', $row['currency']);

    //Render balance
    if ((int)$config->get('balance_mode', 0)) {
        echo $this->_renderRow('balance', $row['balance']);
    } else {
        echo $jbhtml->hidden($this->getRowControlName('balance'), '-1');
    }

    //Render description
    if ((int)$config->get('adv_field_text', 0)) {
        echo $this->_renderRow('description', $row['description']);
    } else {
        echo $jbhtml->hidden($this->getRowControlName('description'), '');
    }

    echo $this->_renderFields($row['params']);
    echo '</fieldset>';
endforeach;