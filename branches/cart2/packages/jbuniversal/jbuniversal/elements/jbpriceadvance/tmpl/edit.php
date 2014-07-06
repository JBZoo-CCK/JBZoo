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

$zoo = $this->app;
$jbhtml = $this->app->jbhtml;
$item = $this->getItem();
$elId = $this->identifier;

$uniqid = uniqid('jsJBPriceAdvance-');

?>
<div class="jbzoo-price-advance jbzoo" id="<?php echo $uniqid; ?>">

    <div class="jbpriceadv-row">
        <label for="<?php echo $elId . '-basic-value'; ?>" class="hasTip row-field"
               title="<?php echo JText::_('JBZOO_JBPRICE_BASIC_VALUE_DESC'); ?>">
            <?php echo JText::_('JBZOO_JBPRICE_BASIC_VALUE'); ?>
        </label>
        <?php
        echo $this->app->html->_('control.text', $this->getControlName('value'), $basicData['value'], array(
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
            echo $currency, $jbhtml->hidden($this->getControlName('currency'), $currency, 'class="basic-currency"');
        } else {
            echo $jbhtml->select($currencyList, $this->getControlName('currency'), 'class="basic-currency" style="width: auto;"', $basicData['currency']);
        }
        ?>
    </div>

    <div class="jbpriceadv-row">
        <label for="<?php echo $elId . '-basic-discount'; ?>" class="hasTip row-field"
               title="<?php echo JText::_('JBZOO_JBPRICE_BASIC_DISCOUNT_DESC'); ?>">
            <?php echo JText::_('JBZOO_JBPRICE_BASIC_DISCOUNT'); ?>
        </label>
        <?php echo $this->app->html->_('control.text', $this->getControlName('discount'), $basicData['discount'], array(
            'size'        => '20',
            'maxlength'   => '255',
            'placeholder' => JText::_('JBZOO_JBPRICE_BASIC_DISCOUNT'),
            'style'       => 'width:100px;',
            'id'          => $elId . '-basic-discount',
            'class'       => 'basic-discount'
        ));

        $currencyList = $zoo->jbarray->unshiftAssoc($currencyList, '%', '%');
        echo $jbhtml->select($currencyList, $this->getControlName('discount_currency'), 'class="basic-currency" style="width: auto;"', $basicData['discount_currency']);
        ?>
    </div>

    <?php if ((int)$config->get('balance_mode', 0)) : ?>
        <div class="jbpriceadv-row basic-balance-wrap">
            <label for="<?php echo $elId . '-basic-balance'; ?>" class="hasTip row-field"
                   title="<?php echo JText::_('JBZOO_JBPRICE_BASIC_BALANCE_DESC'); ?>">
                <?php echo JText::_('JBZOO_JBPRICE_BASIC_BALANCE'); ?>
            </label>

            <?php
            echo $this->app->html->_('control.text', $this->getControlName('balance'), $basicData['balance'], array(
                'size'        => '20',
                'maxlength'   => '255',
                'placeholder' => JText::_('JBZOO_JBPRICE_BASIC_BALANCE'),
                'style'       => 'width:100px;',
                'id'          => $elId . '-basic-balance',
                'class'       => 'basic-balance'
            ));
            ?>
            <div class="clr"></div>
        </div>
    <?php else : ?>

        <div class="jbpriceadv-row jbpriceadv-row-radio basic-balance-wrap">
            <label for="<?php echo $elId . '-basic-balance-bool'; ?>" class="hasTip row-field"
                   title="<?php echo JText::_('JBZOO_JBPRICE_BASIC_BALANCE_BOOL_DESC'); ?>">
                <?php echo JText::_('JBZOO_JBPRICE_BASIC_BALANCE_BOOL'); ?>
            </label>

            <?php
            $list = array(
                '0'  => JText::_('JBZOO_NO'),
                '-1' => JText::_('JBZOO_YES'),
            );

            echo $jbhtml->radio($list, $this->getControlName('balance'), '', $basicData['balance']);
            ?>
            <div class="clr"></div>
        </div>
    <?php endif; ?>

    <div class="jbpriceadv-row">
        <label for="<?php echo $elId . '-basic-sku'; ?>" class="hasTip row-field"
               title="<?php echo JText::_('JBZOO_JBPRICE_BASIC_SKU_DESC'); ?>">
            <?php echo JText::_('JBZOO_JBPRICE_BASIC_SKU'); ?>
        </label>
        <?php echo $this->app->html->_('control.text', $this->getControlName('sku'), $basicData['sku'], array(
            'size'        => '20',
            'maxlength'   => '255',
            'placeholder' => JText::_('JBZOO_JBPRICE_BASIC_SKU'),
            'style'       => 'width:230px;text-align:left;',
            'id'          => $elId . '-basic-sku',
            'class'       => 'basic-sku',
        )); ?>
    </div>

    <div class="jbpriceadv-row jbpriceadvimage">
        <label for="<?php echo $elId . '-basic-image'; ?>" class="hasTip row-field"
               title="<?php echo JText::_('JBZOO_JBPRICE_BASIC_IMAGE_DESC'); ?>">
            <?php echo JText::_('JBZOO_JBPRICE_BASIC_IMAGE'); ?>
        </label>
        <?php echo $this->app->html->_('control.text', $this->getControlName('file'), $basicData['file'],
            'class="jsJBPriceImage" size="60" style="width:200px;margin-right:5px;" title="' . JText::_('File') . '"'); ?>
    </div>

    <?php if ((int)$config->get('adv_field_text', 0)) : ?>
        <div class="jbpriceadv-row">
            <label for="<?php echo $elId . '-basic-discount'; ?>" class="hasTip row-field"
                   title="<?php echo JText::_('JBZOO_JBPRICE_BASIC_DESCRIPTION'); ?>">
                <?php echo JText::_('JBZOO_JBPRICE_BASIC_DESCRIPTION'); ?>
            </label>
            <?php echo $this->app->html->_('control.text', $this->getControlName('description'), $basicData['description'], array(
                'size'        => '20',
                'maxlength'   => '255',
                'placeholder' => JText::_('JBZOO_JBPRICE_BASIC_DESCRIPTION'),
                'style'       => 'width:230px;text-align:left;',
                'id'          => $elId . '-basic-description',
                'class'       => 'basic-description'
            ));
            ?>
        </div>
    <?php endif; ?>

    <div class="jbpriceadv-row jbpriceadv-row-radio">
        <label for="<?php echo $elId . '-basic-new-bool'; ?>" class="hasTip row-field"
               title="<?php echo JText::_('JBZOO_JBPRICE_BASIC_NEW_BOOL_DESC'); ?>">
            <?php echo JText::_('JBZOO_JBPRICE_BASIC_NEW_BOOL'); ?>
        </label>

        <?php echo $this->app->html->_('select.booleanlist', $this->getControlName('new'), '', $basicData['new']); ?>
    </div>

    <div class="jbpriceadv-row jbpriceadv-row-radio">
        <label for="<?php echo $elId . '-basic-hit-bool'; ?>" class="hasTip row-field"
               title="<?php echo JText::_('JBZOO_JBPRICE_BASIC_HIT_BOOL_DESC'); ?>">
            <?php echo JText::_('JBZOO_JBPRICE_BASIC_HIT_BOOL'); ?>
        </label>

        <?php echo $this->app->html->_('select.booleanlist', $this->getControlName('hit'), '', $basicData['hit']); ?>
    </div>
    <?php if ((int)$config->get('mode') && count($variations) >= 1) : ?>
        <a href="#show-variations"
           class="jbajaxlink jsShowVariations"><?php echo JText::_('JBZOO_JBPRICE_VARIATION_SHOW'); ?></a>

        <div class="variations" style="display: none;">
            <div class="variations-list">
                <?php if ($variationsTmpl) {
                    echo $variationsTmpl;
                }
                ?>
            </div>

            <a href="#new-price" class="jbajaxlink jsNewPrice">
                <?php echo JText::_('JBZOO_JBPRICE_VARIATION_NEW'); ?></a>
        </div>
    <?php endif; ?>

</div>

<script type="text/javascript">
    jQuery(function ($) {
        $('#<?php echo $uniqid;?>').JBZooPriceAdvanceAdmin({
            'text_variation_show': "<?php echo JText::_('JBZOO_JBPRICE_VARIATION_SHOW'); ?>",
            'text_variation_hide': "<?php echo JText::_('JBZOO_JBPRICE_VARIATION_HIDE'); ?>",
            'adv_field_param_edit': <?php echo (int)$config->get('adv_field_param_edit', 0); ?>,
            'all_params': <?php echo json_encode(array(
                                        'param1' => $config->get('adv_field_param_1'),
                                        'param2' => $config->get('adv_field_param_2'),
                                        'param3' => $config->get('adv_field_param_3'),
                                    )) ;?>
        });
    });
</script>
