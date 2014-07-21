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

    <!--<div class="jbpriceadv-row">
        <label for="<?php //echo $elId . '-basic-value'; ?>" class="hasTip row-field"
               title="<?php //echo JText::_('JBZOO_JBPRICE_BASIC_VALUE_DESC'); ?>">
            <?php //echo JText::_('JBZOO_JBPRICE_BASIC_VALUE'); ?>
        </label>
        <?php
        /*echo $this->app->html->_('control.text', $this->getControlName('value'), $basicData['value'], array(
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
        */?>
    </div>-->

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
            'all_params': <?php echo json_encode(array(
                                        'param1' => $config->get('adv_field_param_1'),
                                        'param2' => $config->get('adv_field_param_2'),
                                        'param3' => $config->get('adv_field_param_3'),
                                    )) ;?>
        });
    });
</script>
