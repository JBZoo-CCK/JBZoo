<?php
/**
 * JBZoo App is universal Joomla CCK, application for YooTheme Zoo component
 * @package     jbzoo
 * @version     2.x Pro
 * @author      JBZoo App http://jbzoo.com
 * @copyright   Copyright (C) JBZoo.com,  All rights reserved.
 * @license     http://jbzoo.com/license-pro.php JBZoo Licence
 * @coder       Denis Smetannikov <denis@jbzoo.com>
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
        <a rel="nofollow" href="<?php echo $favoriteUrl; ?>" class="jbbutton yellow small">
            <?php echo JText::_('JBZOO_FAVORITE'); ?>
        </a>

        <span class="jbbutton yellow small jsFavoriteToggle"><?php echo JText::_('JBZOO_FAVORITE_REMOVE'); ?></span>
    </div>

    <div class="jbfavorite-unactive">
        <span class="jbbutton yellow small jsFavoriteToggle"><?php echo JText::_('JBZOO_FAVORITE_ADD'); ?></span>
    </div>

</div><!--/noindex-->

<?php echo $this->app->jbassets->widget('#' . $uniqId, 'JBZooFavoriteButtons', array(
    'url_toggle' => $ajaxUrl,
), true); ?>
