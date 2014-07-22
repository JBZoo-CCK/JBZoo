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
$item   = $this->getItem();
$elId   = $this->identifier;
$uniqid = uniqid('jsJBPriceAdvance-');

$basicData = $this->app->data->create($basicData);
$basicParams = $this->app->data->create($basicData->get('params'));

?>

<div class="jbzoo-price-advance jbzoo" id="<?php echo $uniqid; ?>">

    <div class="jbpriceadv-row">
        <label for="<?php echo $elId . '-basic-value'; ?>"
               title="<?php echo JText::_('JBZOO_JBPRICE_BASIC_VALUE_DESC'); ?>"
               class="hasTip row-field"
            >
            <?php echo JText::_('JBZOO_JBPRICE_BASIC_VALUE'); ?>
        </label>

        <?php echo $jbhtml->text($this->getControlName('_value'), $basicData->get('_value'),
            $jbhtml->buildAttrs(
                array(
                    'size'        => '10',
                    'maxlength'   => '255',
                    'placeholder' => JText::_('JBZOO_JBPRICE_BASIC_VALUE'),
                    'style'       => 'width:100px;',
                    'id'          => $elId . '-basic-value',
                    'class'       => 'basic-value',
                )
            )
        );

        if (count($currencyList) == 1) {
            reset($currencyList);
            $currency = current($currencyList);
            echo $currency, $jbhtml->hidden($this->getControlName('_currency'), $currency, 'class="basic-currency"');
        } else {
            echo $jbhtml->select($currencyList, $this->getControlName('_currency'), 'class="basic-currency" style="width: auto;"', $basicData->get('_currency'));
        }
        ?>
    </div>

    <div class="jbpriceadv-row">
        <label for="<?php echo $elId . '-basic-value'; ?>"
               class="hasTip row-field"
               title="<?php echo JText::_('JBZOO_JBPRICE_BASIC_SKU_DESC'); ?>"
            >
            <?php echo JText::_('JBZOO_JBPRICE_BASIC_SKU'); ?>
        </label>
        <?php echo $jbhtml->text($this->getControlParamName('_sku'), $basicParams->get('_sku'),
            $jbhtml->buildAttrs(
                array(
                    'placeholder' => JText::_('JBZOO_JBPRICE_BASIC_SKU'),
                    'style'       => 'width:100px;',
                    'id'          => $elId . '-basic-sku',
                    'class'       => 'basic-sku',
                )
            )
        );?>
    </div>

    <?php echo $basic; ?>

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
        });
    });
</script>
