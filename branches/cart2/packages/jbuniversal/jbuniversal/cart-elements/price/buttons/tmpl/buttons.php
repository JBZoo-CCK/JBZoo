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
defined('_JEXEC') or die('Restricted access'); ?>

<div class="jbprice-buttons jsPriceButtons <?php echo $inCart; ?>">
    <?php $add_btn = $one_btn = $popup_btn = $goto_btn = '';

    if ($add) {
        $add_btn = '<span class="jsAddToCart jbbutton green add-button" title="' . $addLabel . '">'
            . $addLabel
            . '</span>';
    }

    if ($oneClick) {
        $one_btn = '<span class="jsAddToCart jsAddToCartGoTo jbbutton green" title="' . $oneClickLabel . '">'
            . $oneClickLabel
            . '</span>';
    }

    if ($popup) {
        $this->app->jbassets->fancybox();
        $popup_btn = '<span class="jsAddToCartModal jbbutton green" title="' . $popupLabel . '">'
            . $popupLabel
            . '</span>';
    }

    if ($goto) {
        $goto_btn = '<a class="jbbutton goto-button" title="' . $goToLabel . '" href="' . $basketUrl . '">'
            . $goToLabel
            . '</a>';
    }

    echo $add_btn, $one_btn, $popup_btn, $goto_btn; ?>

    <span class="jsRemoveFromCart jsRemoveElement jbbutton small orange remove-button"
          title="<?php echo $removeLabel; ?>">
        <?php echo $removeLabel; ?>
    </span>

</div>
