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

$jbhtml = $this->app->jbhtml;
$elId = $this->app->jbstring->getId();

foreach ($variations as $rowKey => $row) : ?>
    <?php $key = $rowKey + 1; ?>
    <fieldset class="jbpriceadv-variation-row">

    <span class="jbmove"></span>
    <span class="jbremove"></span>

    <div class="default_variant">
        <?php $data = array($key => JText::_('JBZOO_JBPRICE_DEFAULT_VARIANT')); ?>
        <?php echo $jbhtml->radio($data, $this->getControlName('default_variant')); ?>
    </div>

    <span class="variation-label">
        <a href="javascript:void(0);" class="jsToggleVariation">
            <?php echo JText::_('JBZOO_JBPRICE_VARIATION_ROW'); ?>
            #<span class="list-num"><?php echo $key; ?></span>
        </a>
    </span>

    <div class="variant-value-wrap">
        <label for="<?php echo $elId . '-basic-value'; ?>" class="hasTip row-field"
               title="<?php echo JText::_('JBZOO_JBPRICE_VARIATION_VALUE_DESC'); ?>">
            <?php echo JText::_('JBZOO_JBPRICE_VARIATION_VALUE'); ?>
        </label>
        <?php

        echo $this->app->html->_('control.text', $this->getRowControlName('_value'), $row['_value'], array(
            'size'        => '10',
            'maxlength'   => '255',
            'placeholder' => JText::_('JBZOO_JBPRICE_BASIC_VALUE'),
            'style'       => 'width:100px;',
            'id'          => $elId . '-basic-value',
            'class'       => 'basic-value',
        ));

        if (count($currencyList) == 1) {
            reset($currencyList);
            $currency = current($currencyList);
            echo $currency, $jbhtml->hidden($this->getRowControlName('_currency'), $currency, 'class="basic-currency"');
        } else {
            echo $jbhtml->select($currencyList, $this->getRowControlName('_currency'), 'class="basic-currency" style="width: auto;"', $row['_currency']);
        }
        ?>
    </div>
    <div class="variant-sku-wrap">
        <label for="<?php echo $elId . '-variant-sku'; ?>" class="hasTip row-field"
               title="<?php echo JText::_('JBZOO_JBPRICE_VARIATION_SKU_DESC'); ?>">
            <?php echo JText::_('JBZOO_JBPRICE_VARIATION_SKU'); ?>
        </label>
        <?php

        //Render sku input
        echo $this->_renderRow('_sku', $row['params']['_sku']);
        ?>
    </div>

    <?php

    $renderer = $this->app->jbrenderer->create('jbprice');


    echo $renderer->render('_edit',
        array(
            'price' => $this,
            'style' => 'variations',
            'data'  => $row
        )
    );


    echo '</fieldset>';
endforeach;
