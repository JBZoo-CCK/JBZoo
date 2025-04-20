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
    <h2 class="item-title"><?php echo $this->renderPosition('title'); ?></h2>
<?php endif; ?>

<div class="uk-grid">
    <div class="uk-width-1-3">
        <?php if ($this->checkPosition('image')) : ?>
            <div class="item-image uk-align-<?php echo $align; ?>">
                <?php echo $this->renderPosition('image'); ?>
            </div>
        <?php endif; ?>
    </div>
    <div class="uk-width-2-3">
        <?php if ($this->checkPosition('properties')) : ?>
            <table class="item-properties jbtable">
                <?php echo $this->renderPosition('properties', array('style' => 'jbtable', 'tooltip' => true)); ?>
            </table>
        <?php endif; ?>
    </div>
</div>

<?php if ($this->checkPosition('text')) : ?>
    <br />
    <br />
    <?php echo $this->renderPosition('text', array('style' => 'block')); ?>
<?php endif; ?>

<?php if ($this->checkPosition('meta')) : ?>
    <div class="uk-clearfix item-metadata">
        <ul class="uk-list">
            <?php echo $this->renderPosition('meta', array('style' => 'list')); ?>
        </ul>
    </div>
<?php endif; ?>
<hr />