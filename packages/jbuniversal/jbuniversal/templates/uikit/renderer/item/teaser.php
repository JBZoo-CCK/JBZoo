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

$align = $this->app->jbitem->getMediaAlign($item, $layout);
?>

<?php if ($this->checkPosition('title')) : ?>
    <h4 class="item-title"><?php echo $this->renderPosition('title'); ?></h4>
<?php endif; ?>

<div class="uk-grid">
    <?php if ($this->checkPosition('image')) : ?>
        <div class="uk-width-medium-1-2">
            <div class="item-image uk-align-<?php echo $align; ?>">
                <?php echo $this->renderPosition('image'); ?>
            </div>
        </div>
    <?php endif; ?>

    <?php if ($this->checkPosition('properties')) : ?>
        <div class="uk-width-medium-1-2">
            <div class="item-properties">
                <ul class="uk-list uk-list-line">
                    <?php echo $this->renderPosition('properties', array('style' => 'list')); ?>
                </ul>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php if ($this->checkPosition('text')) : ?>
    <div class="item-text uk-article-divider">
        <?php echo $this->renderPosition('text', array('style' => 'block')); ?>
    </div>
<?php endif; ?>

<?php if ($this->checkPosition('buttons')) : ?>
    <div class="item-buttons">
        <?php echo $this->renderPosition('buttons'); ?>
    </div>
<?php endif; ?>

