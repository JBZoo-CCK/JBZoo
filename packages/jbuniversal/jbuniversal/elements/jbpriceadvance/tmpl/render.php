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

$uniqClass = 'jsJBPriceAdvance-' . $this->identifier . '-' . $this->getItem()->id;
$unique = $this->app->jbstring->getId('jbprice-adv-');

?>

<div class="jsJBPriceAdvance jbprice-advance <?php echo $uniqClass; ?>" id="<?php echo $unique; ?>">
    <?php echo $elements; ?>
</div>

<script type="text/javascript">
    (function ($) {

        $('#<?php echo $unique;?>').JBZooPriceAdvance({
            'params': <?php echo json_encode($interfaceParams);?>,
            'prices': <?php echo json_encode($prices);?>,
            'default_variant': <?php echo json_encode($default_variant); ?>,

            'mainHash': "<?php echo $this->_getHash();?>",
            'itemId': <?php echo $this->getItem()->id;?>,
            'identifier': "<?php echo $this->identifier;?>",

            'isInCart': <?php echo $isInCart;?>,

            'addToCartUrl': "<?php echo $addToCartUrl; ?>",
            'removeFromCartUrl': "<?php echo $removeFromCartUrl; ?>",
            'changeVariantUrl': "<?php echo $changeVariantUrl; ?>",
            'modalUrl': "<?php echo $modalUrl; ?>",
            'basketUrl': "<?php echo $basketUrl; ?>"
        });

    })(jQuery);
</script>
