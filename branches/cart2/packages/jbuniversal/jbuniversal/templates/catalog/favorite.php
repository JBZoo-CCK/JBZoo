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


$this->app->jbdebug->mark('template::favorite::start');
$this->app->jblayout->setView($this);
$this->app->document->setTitle(JText::_('JBZOO_FAVORITE_ITEMS'));
$this->app->jbwrapper->start();
$this->app->jbassets->favorite();

?>
    <h1 class="title"><?php echo JText::_('JBZOO_FAVORITE_ITEMS'); ?></h1>

    <div class="jbfavorite-wrapper jsFavoriteList">
        <?php
        if (!empty($this->items)) {
            ?>

            <div class="clearfix">
                <span class="jsFavoriteClear jbfavorite-remove-all jbbutton orange">
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
        'url_clear'        => $this->app->jbrouter->favoriteClear(),
        'text_confirm'     => JText::_('JBZOO_FAVORITE_ITEMS_REMOVE_CONFIRM'),
        'text_confirm_all' => JText::_('JBZOO_FAVORITE_ITEMS_REMOVE_CONFIRM_ALL'),
    ), true); ?>

<?php
$this->app->jbwrapper->end();
$this->app->jbdebug->mark('template::favorite::finish');
