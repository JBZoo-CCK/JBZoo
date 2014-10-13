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

$html = $this->app->jbhtml;
$id = $this->app->jbstring->getId();

foreach ($variations as $rowKey => $row) :

    $params = isset($row['params']) ? $row['params'] : array();
    ?>
    <fieldset class="jbpriceadv-variation-row">

    <span class="jbedit jsToggleVariation"></span>
    <span class="jbremove jsJBRemove"></span>

    <div class="variation-label visible">
        <a href="javascript:void(0);" class="jsJBMove jbmove">
            <?php echo JText::_('JBZOO_JBPRICE_VARIATION_ROW'); ?>
            #<span class="list-num"><?php echo $rowKey + 1; ?></span>
        </a>

        <div class="options">
            <span class="attention jsAttention"></span>
            <span class="variant-price jsVariantPrice"></span>

            <div class="overflow"></div>
        </div>
        <div class="default_variant">
            <?php $data = array($rowKey => JText::_('JBZOO_JBPRICE_DEFAULT_VARIANT')); ?>
            <?php echo $html->radio($data, $this->getControlName('default_variant'), array(
                'id' => $this->app->jbstring->getId('default-variant')
            ), $variant, $this->app->jbstring->getId('default-variant')); ?>
        </div>
        <div class="description"></div>
    </div>

    <div class="jbprice-params">
    <div class="variant-value-wrap core-param variant-param">
        <strong class="hasTip row-field label"
                title="<?php echo JText::_('JBZOO_JBPRICE_VARIATION_VALUE_DESC'); ?>">
            <?php echo JText::_('JBZOO_JBPRICE_VARIATION_VALUE'); ?>
        </strong>
        <span class="attention jsJBPriceAttention"></span>

        <div class="field">
            <?php

            $rowValue = isset($row['_value']['value']) ? $row['_value']['value'] : NULL;
            $rowCurrency = isset($row['_currency']['value']) ? $row['_currency']['value'] : NULL;
            echo $this->app->html->_('control.text', $this->getRowControlName('_value', 'value'),
                $rowValue, array(
                    'size'        => '10',
                    'maxlength'   => '255',
                    'placeholder' => JText::_('JBZOO_JBPRICE_BASIC_VALUE'),
                    'id'          => $id . '-variant-value',
                    'class'       => 'variant-value',
                ));

            if (count($currencyList) == 1) {
                reset($currencyList);
                $currency = current($currencyList);
                echo $currency, $html->hidden($this->getRowControlName('_currency', 'value'), $currency,
                    'class="variant-currency"');
            } else {
                echo $html->select($currencyList, $this->getRowControlName('_currency', 'value'),
                    'class="variant-currency" style="width: auto;"', $rowCurrency);
            }
            ?>
        </div>
    </div>

    <div class="variant-sku-wrap core-param variant-param">
        <strong class="hasTip row-field label"
                title="<?php echo JText::_('JBZOO_JBPRICE_VARIATION_SKU_DESC'); ?>">
            <?php echo JText::_('JBZOO_JBPRICE_VARIATION_SKU'); ?>
        </strong>
        <span class="attention jsJBPriceAttention"></span>

        <div class="field">
            <?php

            $sku = isset($params['_sku']['value']) ? $params['_sku']['value'] : NULL;
            //Render sku input
            echo $this->_renderRow('_sku', 'value', $sku);
            ?>
        </div>
    </div>
    <?php

    echo $renderer->render('_edit',
        array(
            '_variant' => $rowKey,
            'price'    => $this,
            'style'    => ElementJBPriceAdvance::VARIANT_GROUP
        )
    );

    echo '</div></fieldset>';
endforeach;
