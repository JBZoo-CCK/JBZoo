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

$this->app->jbassets->jbzooLinks();
$align = $this->app->jbitem->getMediaAlign($item, $layout);
?>

<?php if ($this->checkPosition('title')) : ?>
    <h1 class="item-title"><?php echo $this->renderPosition('title'); ?></h1>
<?php endif; ?>

<div class="uk-panel uk-card item-body">
    <div class="head-wrapper uk-clearfix">
        <?php if ($this->checkPosition('image')) : ?>
            <div class="item-image uk-align-<?php echo $align; ?>">
                <?php echo $this->renderPosition('image'); ?>
            </div>
        <?php endif; ?>

        <?php if ($this->checkPosition('description')) : ?>
            <div class="item-description">
                <?php echo $this->renderPosition('description', array('style' => 'block')); ?>
            </div>
        <?php endif; ?>
    </div>

    <?php if ($this->checkPosition('properties')) : ?>
        <div class="item-properties">
            <h3><?php echo JText::_('JBZOO_QUICKVIEW_SPECIFICATION'); ?></h3>
            <table class="uk-table uk-table uk-table-striped">
                <?php echo $this->renderPosition('properties', array(
                    'tooltip' => true,
                    'style'   => 'jbtable',
                )); ?>
            </table>
        </div>
    <?php endif; ?>

    <?php if ($this->checkPosition('price')) : ?>
        <div class="item-price jsCartModal">
            <?php echo $this->renderPosition('price'); ?>
        </div>
    <?php endif; ?>

    <?php if ($this->checkPosition('bottom')) : ?>
        <div class="item-bottom uk-clearfix">
            <?php echo $this->renderPosition('bottom', array('style' => 'block')); ?>
        </div>
    <?php endif; ?>
</div>
