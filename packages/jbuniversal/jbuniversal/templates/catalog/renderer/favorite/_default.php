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


$view = $this->getView();

$this->app->jbassets->initJBFavorite();

if (count($vars['objects'])) {

    foreach ($vars['objects'] as $id => $item) {

        $layout = $this->app->jblayout->_getItemLayout($item, 'favorite');

        echo '<div class="jsJBZooFavorite favorite-item-wrapper rborder item-' . $item->id . '">';

        echo '<a class="jbbutton jsJBZooFavoriteRemove" href="' . $this->app->jbrouter->favoriteRemoveItem($item->id) . '" '
            . ' title="' . JText::_('JBZOO_FAVORITE_REMOVE_ITEM') . '">' . JText::_('JBZOO_FAVORITE_REMOVE') . '</a>';

        echo $view->renderer->render($layout, array(
            'view' => $view,
            'item' => $item
        ));

        echo '</div>';
    }

} else {
    echo JText::_('JBZOO_FAVORITE_EMPTY');
}
