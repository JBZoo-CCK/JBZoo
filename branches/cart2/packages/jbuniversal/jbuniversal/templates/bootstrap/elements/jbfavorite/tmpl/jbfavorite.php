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
$jbtemplate = $this->app->zoo->getApplication()->jbtemplate;
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
        <a rel="nofollow" href="<?php echo $favoriteUrl; ?>" class="btn btn-primary" title="<?php echo JText::_('JBZOO_FAVORITE_ITEMS'); ?>">
            <?php echo $jbtemplate->icon('heart', array('type' => 'white')); ?>
        </a>

        <span class="btn btn-danger jsFavoriteToggle" title="<?php echo JText::_('JBZOO_FAVORITE_REMOVE_ITEM'); ?>">
            <?php echo $jbtemplate->icon('trash', array('type' => 'white')); ?>
        </span>
    </div>

    <div class="jbfavorite-unactive">
        <span class="btn btn-primary jsFavoriteToggle" title="<?php echo JText::_('JBZOO_FAVORITE_ADD'); ?>">
            <?php echo $jbtemplate->icon('heart', array('type' => 'white')); ?>
        </span>
    </div>
</div><!--/noindex-->

<?php echo $this->app->jbassets->widget('#' . $uniqId, 'JBZooFavoriteButtons', array(
    'url_toggle' => $ajaxUrl,
), true); ?>
