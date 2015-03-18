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

    <?php if ($add) : ?>
        <span class="jsAddToCart uk-button uk-button-success add-button" title="<?php echo $addLabel; ?>">
            <i class="uk-icon-shopping-cart"></i>
            <?php echo $addLabel; ?>
            </span>
    <?php endif;

    if ($oneClick) : ?>
        <span class="jsAddToCart jsGoTo uk-button uk-button-success" title="<?php echo $oneClickLabel; ?>">
            <i class="uk-icon-external-link-square"></i>
            <?php echo $oneClickLabel; ?>
        </span>
    <?php endif;

    if ($popup && !$isModal)  :
        $this->app->jbassets->fancybox(); ?>

        <span class="jsAddToCartModal uk-button uk-button-success" title="<?php echo $popupLabel; ?>">
            <i class="uk-icon-picture-o"></i>
            <?php echo $popupLabel; ?>
        </span>
    <?php endif;

    if ($goto) : ?>
        <span class="jsPriceButton jsGoTo uk-button goto-button" title="<?php echo $goToLabel; ?>">
            <i class="uk-icon-level-up"></i>
            <?php echo $goToLabel; ?>
        </span>
    <?php endif; ?>

    <a class="jsRemoveFromCart uk-button uk-button-danger uk-button-small remove-button"
       title="<?php echo $removeLabel; ?>">
        <i class="uk-icon-trash-o"></i>
        <?php echo $removeLabel; ?>
    </a>

</div>
