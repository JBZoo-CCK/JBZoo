<?php
/**
 * JBZoo App is universal Joomla CCK, application for YooTheme Zoo component
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

$string = $this->app->jbstring;
$unique = $string->getId('jsJBPrice-');

$isAdvance  = $countSimple >= 1 ? 1 : 0;
$price_mode = (get_class($this) === 'ElementJBPriceCalc' ? 2 : 1); ?>

<div class="jbzoo-price jbzoo <?php echo $hash; ?>" id="<?php echo $unique; ?>" data-mode="<?php echo $isAdvance; ?>"
     data-valid="false">

    <div class="default_variant basic-variant-wrap">
        <?php if ($countSimple) {
            $data = array(0 => JText::_('JBZOO_JBPRICE_DEFAULT_VARIANT'));
            echo $html->radio($data, $this->getControlName('default_variant'), array(
                'id' => $string->getId('default-variant')
            ), $default, $string->getId('default-variant'));
        } else {
            echo $html->hidden($this->getControlName('default_variant'), ElementJBPrice::BASIC_VARIANT);
        } ?>
    </div>

    <?php
    $variant = $variations[ElementJBPrice::BASIC_VARIANT];
    echo $renderer->render('_edit', array(
        'element_id' => $this->identifier,
        '_variant'   => $variant
    ));

    $count = count($variations);
    if ($countSimple) : ?>
        <span class="jsShowVariations jbbutton small">
            <?php echo JText::_('JBZOO_JBPRICE_VARIATION_SHOW'); ?>
        </span>

        <div class="variations" style="display: none;">
            <div class="variations-list">
                <?php
                for ($i = 1; $i < $count; $i++) :
                    $variant = $variations[$i]; ?>
                    <fieldset class="jbprice-variation-row fieldset-hidden jsVariant jsVariant-<?php echo $i; ?>">

                        <span class="jbedit jsToggleVariation"></span>
                        <span class="jbremove jsJBRemove"></span>

                        <div class="variation-label jsVariantLabel visible">

                            <a href="javascript:void(0);" class="jsJBMove jbmove">
                                <?php echo JText::_('JBZOO_JBPRICE_VARIATION_ROW'); ?>
                                #<span class="list-num"><?php echo $i; ?></span>
                            </a>

                            <div class="options">
                                <span class="attention jsMessage"></span>
                                <span class="variant-price jsVariantPrice"></span>

                                <div class="overflow"></div>
                            </div>

                            <div class="default_variant">
                                <?php $data = array($i => JText::_('JBZOO_JBPRICE_DEFAULT_VARIANT'));
                                echo $html->radio($data, $this->getControlName('default_variant'), array(
                                    'id' => $string->getId('default-variant')
                                ), $default, $string->getId('default-variant')); ?>
                            </div>

                            <div class="description"></div>
                        </div>

                        <div class="jbprice-params">
                            <?php echo $renderer->render('_edit', array(
                                'element_id' => $this->identifier,
                                '_variant'   => $variant
                            ));?>
                        </div>
                    </fieldset>
                <?php
                endfor;
                ?>
            </div>

            <a href="#new-price" class="jbajaxlink jsNewPrice">
                <?php echo JText::_('JBZOO_JBPRICE_VARIATION_NEW'); ?>
            </a>
        </div>
    <?php
    else :
        echo $this->renderWarning();
    endif;
    ?>

</div>

<?php echo $this->app->jbassets->widget('#' . $unique, 'JBZoo.PriceEdit', array(
    'isAdvance'                  => $isAdvance,
    'text_show'                  => JText::_('JBZOO_JBPRICE_VARIATION_SHOW'),
    'text_hide'                  => JText::_('JBZOO_JBPRICE_VARIATION_HIDE'),
    'isOverlay'                  => (bool)$this->isOverlay,
    'validator_variant_invalid'  => JText::_('JBZOO_JBPRICE_VALIDATOR_VARIANT_INVALID'),
    'validator_duplicate_values' => JText::_('JBZOO_JBPRICE_VALIDATOR_DUPLICATE_VALUES'),
    'validator_choice_limiting'  => JText::_('JBZOO_JBPRICE_VALIDATOR_CHOOSE_LIMITING')
), true); ?>

<script type="text/javascript">

    function submitbutton(pressbutton) {

        var jbprices = jQuery('.jbzoo-price');

        if (pressbutton == 'cancel') {
            submitform(pressbutton);
        } else {

            var valid = true;
            jbprices.each(function (i, jbprice) {

                var validator = jQuery(jbprice).data('JBZooPriceEdit');

                if (validator && validator.isValid() === false) {
                    valid = false;

                    return true;
                }
            });

            if (valid) {
                submitform(pressbutton);
            }
        }
    }

</script>
