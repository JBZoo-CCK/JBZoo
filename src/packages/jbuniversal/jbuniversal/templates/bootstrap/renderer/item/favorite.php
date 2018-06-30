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

$bootstrap = $this->app->jbbootstrap;
$rowClass   = $bootstrap->getRowClass();
?>

<?php if ($this->checkPosition('title')) : ?>
    <h2 class="item-title"><?php echo $this->renderPosition('title'); ?></h2>
<?php endif; ?>

<div class="<?php echo $rowClass; ?> last">
    <div class="<?php echo $bootstrap->gridClass(4); ?>">
        <?php if ($this->checkPosition('image')) : ?>
            <div class="item-image">
                <?php echo $this->renderPosition('image'); ?>
            </div>
        <?php endif; ?>

        <?php if ($this->checkPosition('price')) : ?>
            <div class="item-price">
                <?php echo $this->renderPosition('price', array('style' => 'jbblock')); ?>
            </div>
        <?php endif; ?>
    </div>

    <div class="<?php echo $bootstrap->gridClass(8); ?>">
        <?php if ($this->checkPosition('text')) : ?>
            <div class="item-text">
                <?php echo $this->renderPosition('text', array(
                    'labelTag' => 'h4',
                    'style'    => 'block',
                )); ?>
            </div>
        <?php endif; ?>

        <?php if ($this->checkPosition('properties')) : ?>
            <table class="table table-hover item-properties">
                <?php echo $this->renderPosition('properties', array('style' => 'jbtable')); ?>
            </table>
        <?php endif; ?>
    </div>
</div>
