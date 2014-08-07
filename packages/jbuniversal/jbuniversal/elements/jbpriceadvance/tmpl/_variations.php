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
$defaultVariant = isset($basicData['default_variant']) ? $basicData['default_variant'] : null;

foreach ($variations as $rowKey => $row) : ?>
    <fieldset class="jbpriceadv-variation-row">

    <span class="jbedit jsJBedit jsToggleVariation"></span>
    <span class="jbremove jsJBremove"></span>

    <div class="variation-label visible">
        <a href="javascript:void(0);" class="jsJBmove jbmove">
            <?php echo JText::_('JBZOO_JBPRICE_VARIATION_ROW'); ?>
            #<span class="list-num"><?php echo $rowKey + 1; ?></span>
            <span class="attention jsAttention"></span>
        </a>

        <div class="options">
            <div class="overflow">

            </div>
        </div>
        <div class="default_variant">
            <?php $data = array($rowKey => JText::_('JBZOO_JBPRICE_DEFAULT_VARIANT')); ?>
            <?php echo $jbhtml->radio($data, $this->getControlName('default_variant'), array(
                'id' => $this->app->jbstring->getId('default-variant')
            ), $defaultVariant, $this->app->jbstring->getId('default-variant')); ?>
        </div>
        <div class="description"></div>
    </div>

    <div class="jbprice-params">
    <div class="variant-value-wrap variant-param">
        <strong class="hasTip row-field label"
                title="<?php echo JText::_('JBZOO_JBPRICE_VARIATION_VALUE_DESC'); ?>">
            <?php echo JText::_('JBZOO_JBPRICE_VARIATION_VALUE'); ?>
        </strong>
        <span class="attention jsJBpriceAttention"></span>

        <div class="field">
            <?php

            echo $this->app->html->_('control.text', $this->getRowControlName('_value'), $row['_value'], array(
                'size'        => '10',
                'maxlength'   => '255',
                'placeholder' => JText::_('JBZOO_JBPRICE_BASIC_VALUE'),
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
    </div>
    <div class="variant-sku-wrap variant-param">
        <strong class="hasTip row-field label"
                title="<?php echo JText::_('JBZOO_JBPRICE_VARIATION_SKU_DESC'); ?>">
            <?php echo JText::_('JBZOO_JBPRICE_VARIATION_SKU'); ?>
        </strong>
        <span class="attention jsJBpriceAttention"></span>

        <div class="field">
            <?php
            //Render sku input
            echo $this->_renderRow('_sku', $row['params']['_sku']);
            ?>
        </div>
    </div>
    <?php

    $renderer = $this->app->jbrenderer->create('jbprice');

    echo $renderer->render('_edit',
        array(
            'index'      => $rowKey,
            'price'      => $this,
            'style'      => 'variations',
            'data'       => $row,
            'price_mode' => $this->config->get('price_mode', 1)
        )
    );


    echo '</div></fieldset>';
endforeach;
