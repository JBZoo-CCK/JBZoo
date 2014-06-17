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


$this->app->jbassets->initJBFavorite();

?>
<!--noindex-->
<div class="wrapper-jbfavorite jsJBZooFavorite <?php echo ($isExists ? ' active ' : 'unactive');?>">

    <div class="active-favorite">
        <a rel="nofollow" href="<?php echo $ajaxUrl;?>" class="jsFavoriteToggle" title="<?php echo JText::_('JBZOO_FAVORITE_REMOVE');?>"><?php echo JText::_('JBZOO_FAVORITE_REMOVE');?></a>
        <a rel="nofollow" href="<?php echo $favoriteUrl;?>" title="<?php echo JText::_('JBZOO_FAVORITE');?>"><?php echo JText::_('JBZOO_FAVORITE');?></a>
    </div>

    <div class="unactive-favorite">
        <a rel="nofollow" href="<?php echo $ajaxUrl;?>" class="jsFavoriteToggle" title="<?php echo JText::_('JBZOO_FAVORITE_ADD');?>"><?php echo JText::_('JBZOO_FAVORITE_ADD');?></a>
    </div>

</div>
<!--/noindex-->
