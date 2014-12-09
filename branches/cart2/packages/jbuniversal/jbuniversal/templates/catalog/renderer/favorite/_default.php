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


$view = $this->getView();

if (count($vars['objects'])) {

    foreach ($vars['objects'] as $id => $item) {
        $this->app->jbassets->favorite();

        ?>
        <div class="jsFavoriteItem jbfavorite-item-wrapper rborder item-<?php echo $item->id; ?>">

            <span class="jbbutton small orange jsFavoriteItemRemove jbfavorite-remove-item"
                  data-url="<?php echo $this->app->jbrouter->favoriteRemoveItem($item->id); ?>">
                <?php echo JText::_('JBZOO_FAVORITE_REMOVE'); ?>
            </span>

            <?php echo $view->renderer->render(
                $this->app->jblayout->_getItemLayout($item, 'favorite'),
                array(
                    'view' => $view,
                    'item' => $item
                )
            ); ?>

        </div>
    <?php
    }

} else {
    echo JText::_('JBZOO_FAVORITE_EMPTY');
}
