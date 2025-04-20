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

$align = $this->app->jbitem->getMediaAlign($item, $layout);
?>

<?php if ($this->checkPosition('title')) : ?>
    <h1 class="item-title"><?php echo $this->renderPosition('title'); ?></h1>
<?php endif; ?>

<?php if ($this->checkPosition('image')) : ?>
    <div class="item-image uk-align-<?php echo $align; ?>">
        <?php echo $this->renderPosition('image'); ?>
    </div>
<?php endif; ?>

<?php if ($this->checkPosition('properties')) : ?>
    <ul class="item-properties">
        <?php echo $this->renderPosition('properties', ['style' => 'list']); ?>
    </ul>
<?php endif; ?>

<?php if ($this->checkPosition('text')) : ?>
    <?php echo $this->renderPosition('text', ['style' => 'block']); ?>
<?php endif; ?>

<?php if ($this->checkPosition('meta')) : ?>
    <div class="item-metadata">
        <ul class="uk-list">
            <?php echo $this->renderPosition('meta', ['style' => 'list']); ?>
        </ul>
    </div>
<?php endif; ?>

<div class="uk-clearfix"></div>
