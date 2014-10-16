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

$id = uniqid('jsJBPriceAdvance-');

$html = $this->app->jbhtml;
$item = $this->getItem();
?>

<div class="jbzoo-price-advance jbzoo" id="<?php echo $id; ?>" data-valid="false">

    <div class="jbpriceadv-row basic-variant-wrap">
        <div class="default_variant">
            <?php $data = array("" => JText::_('JBZOO_JBPRICE_DEFAULT_VARIANT')); ?>
            <?php echo $html->radio($data, $this->getControlName('default_variant'), array(
                'id' => $this->app->jbstring->getId('default-variant')
            ), $variant, $this->app->jbstring->getId('default-variant')); ?>
        </div>
    </div>

    <div class="jbpriceadv-row basic-value-wrap">
        <label for="basic-value"
               title="<?php echo JText::_('JBZOO_JBPRICE_BASIC_VALUE_DESC'); ?>"
               class="hasTip row-field"
            >
            <?php echo JText::_('JBZOO_JBPRICE_BASIC_VALUE'); ?>
        </label>

        <?php echo $html->text($this->getControlName('_value', 'value'), $basicData->find('_value.value', 0),
            $html->buildAttrs(
                array(
                    'type'        => 'text',
                    'size'        => '10',
                    'maxlength'   => '255',
                    'placeholder' => JText::_('JBZOO_JBPRICE_BASIC_VALUE'),
                    'style'       => 'width:100px;',
                    'id'          => 'basic-value',
                    'class'       => 'basic-value'
                )
            ));

        if (count($currencyList) == 1) {
            reset($currencyList);
            $currency = current($currencyList);
            echo $currency, $html->hidden($this->getControlName('_currency', 'value'), $currency,
                'class="basic-currency"');
        } else {
            echo $html->select($currencyList, $this->getControlName('_currency', 'value'),
                'class="basic-currency" style="width: auto;"', $basicData->find('_currency.value', 0));
        }
        ?>
    </div>

    <div class="jbpriceadv-row basic-sku-wrap">
        <label for="basic-sku" class="hasTip row-field"
               title="<?php echo JText::_('JBZOO_JBPRICE_BASIC_SKU_DESC'); ?>"
            >
            <?php echo JText::_('JBZOO_JBPRICE_BASIC_SKU'); ?>
        </label>
        <?php echo $html->text($this->getControlParamName('_sku', 'value'), $basicData->find('_sku.value', $this->getItem()->id),
            $html->buildAttrs(
                array(
                    'placeholder' => JText::_('JBZOO_JBPRICE_BASIC_SKU'),
                    'style'       => 'width:100px;',
                    'id'          => 'basic-sku',
                    'class'       => 'basic-sku',
                )
            )
        );?>
    </div>

    <?php echo $basicHTML;

    if (empty($submission) && (int)$config->get('mode', 0) && count($variations) >= 1
        || !empty($submission) && (int)$submission->get('mode', 0)
    ) : ?>
        <a href="#show-variations"
           class="jbajaxlink jsShowVariations"><?php echo JText::_('JBZOO_JBPRICE_VARIATION_SHOW'); ?></a>

        <div class="variations" style="display: none;">
            <div class="variations-list">
                <?php if ($variantsHTML) {
                    echo $variantsHTML;
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
        $('#<?php echo $id;?>').JBZooPriceAdvanceAdmin({
            'text_variation_show' : "<?php echo JText::_('JBZOO_JBPRICE_VARIATION_SHOW'); ?>",
            'text_variation_hide' : "<?php echo JText::_('JBZOO_JBPRICE_VARIATION_HIDE'); ?>",
            'price_mode'          : <?php echo $this->config->get('price_mode', 1); ?>,
            'adv_field_param_edit': <?php echo (int)$config->get('adv_field_param_edit', 0); ?>
        });

    });

    function submitbutton(pressbutton) {

        var prices = jQuery('.jbzoo-price-advance');

        if (pressbutton == 'cancel') {
            submitform(pressbutton);
        } else {

            var valid = true;
            jQuery(prices).each(function () {

                var price = jQuery(this);

                price.trigger('errorsExists');
                if (price.data('valid') === false) {
                    valid = false;
                }

            });

            if (valid) {
                submitform(pressbutton);
            }
        }
    }

</script>
