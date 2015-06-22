<?php
/**
 * JBZoo App is universal Joomla CCK, application for YooTheme Zoo component
 * @package     jbzoo
 * @version     2.x Pro
 * @author      JBZoo App http://jbzoo.com
 * @copyright   Copyright (C) JBZoo.com,  All rights reserved.
 * @license     http://jbzoo.com/license-pro.php JBZoo Licence
 * @coder       Denis Smetannikov <denis@jbzoo.com>
 * @coder       Sergey Kalistratov <kalistratov.s.m@gmail.com>
 */

// no direct access
defined('_JEXEC') or die('Restricted access');


$this->app->jbassets->favorite();

$uniqId    = $this->app->jbstring->getId('favorite-');
$wrapAttrs = array(
    'id'    => $uniqId,
    'class' => array(
        'jsJBZooFavorite',
        'jbfavorite-buttons',
        $isExists ? ' active ' : 'unactive'
    )
);

?>
<!--noindex-->
<div <?php echo $this->app->jbhtml->buildAttrs($wrapAttrs); ?>>
    <div class="jbfavorite-active">
        <a rel="nofollow" href="<?php echo $favoriteUrl; ?>" class="uk-button uk-button-primary" title="<?php echo JText::_('JBZOO_FAVORITE_ITEMS'); ?>">
            <i class="uk-icon-heart-o"></i>
        </a>

        <span class="uk-button uk-button-danger jsFavoriteToggle" title="<?php echo JText::_('JBZOO_FAVORITE_REMOVE_ITEM'); ?>">
            <i class="uk-icon-trash"></i>
        </span>
    </div>

    <div class="jbfavorite-unactive">
        <span class="uk-button uk-button-primary jsFavoriteToggle" title="<?php echo JText::_('JBZOO_FAVORITE_ADD'); ?>">
            <i class="uk-icon-heart"></i>
        </span>
    </div>
</div><!--/noindex-->

<?php echo $this->app->jbassets->widget('#' . $uniqId, 'JBZooFavoriteButtons', array(
    'url_toggle' => $ajaxUrl,
), true); ?>
