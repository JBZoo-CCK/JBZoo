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

$string     = $this->app->jbstring;
$unique     = $string->getId('jsJBPrice-');
$price_mode = (get_class($this) == 'ElementJBPriceCalc' ? 2 : 1); ?>

<div class="jbzoo-price jbzoo" id="<?php echo $unique; ?>" data-mode="<?php echo $mode; ?>" data-valid="false">

    <div class="jbprice-row basic-variant-wrap">

        <div class="default_variant">
            <?php if ($mode) {
                $data = array(0 => JText::_('JBZOO_JBPRICE_DEFAULT_VARIANT'));
                echo $html->radio($data, $this->getControlName('default_variant'), array(
                    'id' => $string->getId('default-variant')
                ), $default, $string->getId('default-variant'));
            } else {
                echo $html->hidden($this->getControlName('default_variant'), ElementJBPrice::BASIC_VARIANT);
            } ?>
        </div>

    </div>
    <?php for ($i = 0; $i < 1; $i++) :
        $variant = $variations[$i];
        echo $renderer->render('_edit', array(
            'variant'    => $i,
            'element_id' => $this->identifier,
            '_variant'   => $variant
        ));
    endfor;

    if ($mode) : ?>
        <span class="jsShowVariations jbbutton small"><?php echo JText::_('JBZOO_JBPRICE_VARIATION_SHOW'); ?></span>
        <div class="variations" style="display: none;">
            <div class="variations-list">

                <?php for ($i = 1; $i < count($variations); $i++) :
                    if (isset($variations[$i])) :
                        $variant = $variations[$i];?>

                        <fieldset class="jbprice-variation-row fieldset-hidden">

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
                                    'variant'    => $i,
                                    'element_id' => $this->identifier,
                                    '_variant'   => $variant
                                ));?>
                            </div>
                        </fieldset>
                    <?php endif; endfor; ?>
            </div>

            <a href="#new-price" class="jbajaxlink jsNewPrice">
                <?php echo JText::_('JBZOO_JBPRICE_VARIATION_NEW'); ?>
            </a>
        </div>
    <?php endif; ?>
</div>

<?php echo $this->app->jbassets->widget('#' . $unique, 'JBZoo.PriceEdit', array(
    'isAdvance' => $mode,
    'text_show' => JText::_('JBZOO_JBPRICE_VARIATION_SHOW'),
    'text_hide' => JText::_('JBZOO_JBPRICE_VARIATION_HIDE'),
    'isOverlay' => (bool)$this->isOverlay,
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
