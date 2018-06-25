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

$this->app->jbdebug->mark('template::item::start');

$this->app->jblayout->setView($this);
$this->app->jbwrapper->start();

$layout = $this->app->jbrequest->get('jbquickview');

if (method_exists($this->item, 'canEdit') && $this->item->canEdit()) {
    $edit_link = $this->app->route->submission($this->item->getApplication()->getItemEditSubmission(), $this->item->type, null, $this->item->id, 'itemedit');
    ?>
    <div class="item-edit-link uk-margin-bottom">
        <a href="<?php echo JRoute::_($edit_link); ?>" title="<?php echo JText::_('Edit Item'); ?>"
           class="uk-button uk-button-primary edit-item">
            <i class="uk-icon-edit"></i>
            <?php echo JText::_('Edit Item'); ?>
        </a>
    </div>
<?php }

if ($this->app->jblayout->checkLayout($this->item, $layout)) {
    echo $this->app->jblayout->renderItem($this->item, $layout);
} else {
    echo $this->app->jblayout->renderItem($this->item, 'full');

    // render comments (if no rendered in element)
    if (!defined('JBZOO_COMMENTS_RENDERED_' . $this->item->id)) {
        echo $this->app->comment->renderComments($this, $this->item);
    }
}

$this->app->jbwrapper->end();

$this->app->jbdebug->mark('template::item::finish');
