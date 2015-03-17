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
        $add_btn = '<span class="jsAddToCart uk-button uk-button-success add-button" title="' . $addLabel . '">
            <i class="uk-icon-shopping-cart"></i>'
            . $addLabel
            . '</span>';
    }

    if ($oneClick) {
        $one_btn = '<span class="jsAddToCartGoto uk-button uk-button-success" title="' . $oneClickLabel . '">'
            . $oneClickLabel
            . '</span>';
    }

    if ($popup) {
        $this->app->jbassets->fancybox();

        $popup_btn = '<span class="jsAddToCartModal uk-button uk-button-success" title="' . $popupLabel . '">'
            . $popupLabel
            . '</span>';
    }

    if ($goto) {
        $goto_btn = '<a class="uk-button goto-button" title="' . $goToLabel . '" href="' . $basketUrl . '">'
            . '<i class="uk-icon-external-link"></i>'
            . $goToLabel
            . '</a>';
    }

    echo $add_btn, $one_btn, $popup_btn, $goto_btn; ?>

    <span class="jsRemoveFromCart uk-button uk-button-danger uk-button-small remove-button"
       title="<?php echo $removeLabel; ?>">
        <i class="uk-icon-times"></i>
        <?php echo $removeLabel; ?>
    </span>
</div>
