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
        <span class="jsAddToCart jbbutton green add-button" title="<?php echo $addLabel; ?>">
            <?php echo $addLabel; ?>
            </span>
    <?php endif;

    if ($oneClick) : ?>
        <span class="jsAddToCartGoTo jsAddToCart jbbutton green" title="<?php echo $oneClickLabel; ?>">
            <?php echo $oneClickLabel; ?>
        </span>
    <?php endif;

    if ($popup)  :
        $this->app->jbassets->fancybox(); ?>

        <span class="jsAddToCartModal jbbutton green" title="<?php echo $popupLabel; ?>">
            <?php echo $popupLabel; ?>
        </span>
    <?php endif;

    if ($goto) : ?>
        <a class="jbbutton goto-button" title="<?php echo $goToLabel; ?>" href="<?php echo $basketUrl; ?>">
            <?php echo $goToLabel; ?>
        </a>
    <?php endif; ?>

    <a class="jsRemoveFromCart jsRemoveElement jbbutton small orange remove-button"
       title="<?php echo $removeLabel; ?>">
        <?php echo $removeLabel; ?>
    </a>
</div>
