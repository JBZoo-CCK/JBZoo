<?php
/**
 * JBZoo Application
 *
 * This file is part of the JBZoo CCK package.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package    Application
 * @license    GPL-2.0
 * @copyright  Copyright (C) JBZoo.com, All rights reserved.
 * @link       https://github.com/JBZoo/JBZoo
 * @author     Denis Smetannikov <denis@jbzoo.com>
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

$view = $this->getView();

if (count($vars['objects'])) {

    foreach ($vars['objects'] as $id => $item) {
        $this->app->jbassets->favorite();

        ?>
        <div class="jsFavoriteItem jbfavorite-item-wrapper item-<?php echo $item->id; ?>">
            <div class="well clearfix">
                 <span class="btn btn-danger jsFavoriteItemRemove jbfavorite-remove-item"
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
        </div>
    <?php
    }

} else {
    echo JText::_('JBZOO_FAVORITE_EMPTY');
}
