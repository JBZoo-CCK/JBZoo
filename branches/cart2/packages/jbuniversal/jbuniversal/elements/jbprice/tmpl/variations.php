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
$mode = $this->config->get('mode', 0);

$string = $this->app->jbstring;
$unique = $string->getId('jsJBPriceAdvance-');

$price_mode = (get_class($this) == 'ElementJBPriceCalc' ? 2 : 1); ?>

<div class="jbzoo-price-advance jbzoo" id="<?php echo $unique; ?>" data-mode="<?php echo $mode; ?>" data-valid="false">

    <div class="jbpriceadv-row basic-variant-wrap">

        <div class="default_variant">
            <?php
            $data = array(0 => JText::_('JBZOO_JBPRICE_DEFAULT_VARIANT'));
            echo $html->radio($data, $this->getControlName('default_variant'), array(
                    'id' => $string->getId('default-variant')
                ),
                $default,
                $string->getId('default-variant')); ?>
        </div>

    </div>
    <?php for ($i = 0; $i < 1; $i++) :
        $variant = $variations[$i];
        echo $renderer->render('_edit',
            array(
                'variant'  => $i,
                '_variant' => $variant,
                'price'    => $this
            ));
    endfor;

    if ((int)$mode) : ?>

        <a href="#show-variations" class="jbajaxlink jsShowVariations">
            <?php echo JText::_('JBZOO_JBPRICE_VARIATION_SHOW'); ?>
        </a>

        <div class="variations" style="display: none;">

            <div class="variations-list">

                <?php

                for ($i = 1; $i < count($variations); $i++) :

                    $variant = $variations[$i];?>

                    <fieldset class="jbpriceadv-variation-row">

                        <span class="jbedit jsToggleVariation"></span>
                        <span class="jbremove jsJBRemove"></span>

                        <div class="variation-label visible">

                            <a href="javascript:void(0);" class="jsJBMove jbmove">

                                <?php echo JText::_('JBZOO_JBPRICE_VARIATION_ROW'); ?>
                                #<span class="list-num">
                            <?php echo $i; ?>
                        </span>

                            </a>

                            <div class="options">
                                <span class="attention jsAttention"></span>
                                <span class="variant-price jsVariantPrice"></span>

                                <div class="overflow"></div>
                            </div>

                            <div class="default_variant">
                                <?php
                                $data = array($i => JText::_('JBZOO_JBPRICE_DEFAULT_VARIANT'));
                                echo $html->radio($data, $this->getControlName('default_variant'), array(
                                        'id' => $string->getId('default-variant')
                                    ),
                                    $default,
                                    $string->getId('default-variant')); ?>
                            </div>

                            <div class="description"></div>
                        </div>

                        <div class="jbprice-params">

                            <?php echo $renderer->render('_edit',
                                array(
                                    'variant'  => $i,
                                    '_variant' => $variant,
                                    'price'    => $this
                                )
                            ); ?>

                        </div>
                    </fieldset>
                <?php endfor; ?>
            </div>

            <a href="#new-price" class="jbajaxlink jsNewPrice">
                <?php echo JText::_('JBZOO_JBPRICE_VARIATION_NEW'); ?>
            </a>
        </div>
    <?php endif; ?>
</div>

<script type="text/javascript">

    jQuery(function ($) {
        $('#<?php echo $unique;?>').JBZooPriceAdvanceAdmin({
            'text_variation_show': "<?php echo JText::_('JBZOO_JBPRICE_VARIATION_SHOW'); ?>",
            'text_variation_hide': "<?php echo JText::_('JBZOO_JBPRICE_VARIATION_HIDE'); ?>",
            'price_mode'         : <?php echo $price_mode; ?>
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

                if (price.data('mode') > 0) {

                    price.trigger('errorsExists');
                    if (price.data('valid') === false) {
                        valid = false;
                    }
                }

            });

            if (valid) {
                submitform(pressbutton);
            }
        }
    }

</script>

