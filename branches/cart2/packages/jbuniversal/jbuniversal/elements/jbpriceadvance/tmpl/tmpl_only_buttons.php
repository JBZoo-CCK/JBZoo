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


$uniqClass = 'jsJBPriceAdvance-' . $this->identifier . '-' . $this->getItem()->id;
$iniqId = uniqid('jbprice-adv-');
?>

<div class="jsJBPriceAdvance jbprice-advance <?php echo $uniqClass; ?>" id="<?php echo $iniqId; ?>">
    <?php echo $buttonsTmpl; ?>
</div>

<script type="text/javascript">
    (function ($) {

        $('#<?php echo $iniqId;?>').JBZooPriceAdvance({
            'mainHash': "<?php echo $this->_getHash();?>",
            'itemId': <?php echo $this->getItem()->id;?>,
            'identifier': "<?php echo $this->identifier;?>",
            'isInCart': <?php echo $isInCart;?>,
            'addToCartUrl': "<?php echo $addToCartUrl; ?>",
            'removeFromCartUrl': "<?php echo $removeFromCartUrl; ?>",
            'modalUrl': "<?php echo $modalUrl; ?>",
            'basketUrl': "<?php echo $basketUrl; ?>"
        });

    })(jQuery);
</script>
