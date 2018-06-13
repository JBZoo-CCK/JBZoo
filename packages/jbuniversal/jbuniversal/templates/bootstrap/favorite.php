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

$this->app->jbdebug->mark('template::favorite::start');
$this->app->jblayout->setView($this);
$this->app->document->setTitle(JText::_('JBZOO_FAVORITE_ITEMS'));
$this->app->jbwrapper->start();
$this->app->jbassets->favorite();

$bootstrap = $this->app->jbbootstrap;

?>
    <h1 class="title"><?php echo JText::_('JBZOO_FAVORITE_ITEMS'); ?></h1>

    <div class="jbfavorite-wrapper jsFavoriteList">
        <?php
        if (!empty($this->items)) {
            ?>

            <div class="clearfix">
                <span class="jsFavoriteClear jbfavorite-remove-all btn btn-danger">
                    <?php echo $bootstrap->icon('trash'); ?>
                    <?php echo JText::_('JBZOO_FAVORITE_REMOVE_ALL'); ?>
                </span>
            </div>

            <?php
            echo $this->app->jblayout->render('favorite', $this->items);
            echo '<p class="jsJBZooFavoriteEmpty jbfavorite-empty" style="display:none;">'
                . JText::_('JBZOO_FAVORITE_ITEMS_NOT_FOUND') . '</p>';

        } else {
            echo '<p class="jbfavorite-empty">' . JText::_('JBZOO_FAVORITE_ITEMS_NOT_FOUND') . '</p>';
        }
        ?>
    </div>

<?php echo $this->app->jbassets->widget('.jsFavoriteList', 'JBZooFavoriteList', array(
    'url_clear' => $this->app->jbrouter->favoriteClear()
), true); ?>

<?php
$this->app->jbwrapper->end();
$this->app->jbdebug->mark('template::favorite::finish');
