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

    <?php /***** ADD BUTTON *****/
    if ($add) : ?>
        <span class="jsAddToCart jbbutton green add-button" title="<?php echo $addLabel; ?>">
            <?php echo $addLabel; ?>
            </span>
    <?php endif;

    /***** CLICK AND GO BUTTON *****/
    if ($oneClick) : ?>
        <span class="jsAddToCart jsGoTo jbbutton green" title="<?php echo $oneClickLabel; ?>">
            <?php echo $oneClickLabel; ?>
        </span>
    <?php endif;

    /***** POP UP BUTTON *****/
    if ($popup && !$isModal)  :
        $this->app->jbassets->fancybox(); ?>

        <span class="jsAddToCartModal jbbutton green" title="<?php echo $popupLabel; ?>">
            <?php echo $popupLabel; ?>
        </span>
    <?php endif;

    /***** REDIRECT TO BASKET BUTTON *****/
    if ($goto) : ?>
        <span class="jsPriceButton jsGoTo jbbutton goto-button" title="<?php echo $goToLabel; ?>">
            <?php echo $goToLabel; ?>
        </span>
    <?php endif;

    /***** REMOVE BUTTON *****/ ?>
    <a class="jsRemoveFromCart jsRemoveElement jbbutton small orange remove-button"
       title="<?php echo $removeLabel; ?>">
        <?php echo $removeLabel; ?>
    </a>
</div>
