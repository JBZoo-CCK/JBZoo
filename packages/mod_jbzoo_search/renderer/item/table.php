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

?>
<table class="jbfilter-table no-border">
    <tr>
        <?php if ($this->checkPosition('cell_1_1')) : ?>
            <td><?php echo $this->renderPosition('cell_1_1', ['style' => 'filter.block']); ?></td>
        <?php endif; ?>
        <?php if ($this->checkPosition('cell_1_2')) : ?>
            <td><?php echo $this->renderPosition('cell_1_2', ['style' => 'filter.block']); ?></td>
        <?php endif; ?>
        <?php if ($this->checkPosition('cell_1_3')) : ?>
            <td><?php echo $this->renderPosition('cell_1_3', ['style' => 'filter.block']); ?></td>
        <?php endif; ?>
        <?php if ($this->checkPosition('cell_1_4')) : ?>
            <td><?php echo $this->renderPosition('cell_1_4', ['style' => 'filter.block']); ?></td>
        <?php endif; ?>
    </tr>
    <tr>
        <?php if ($this->checkPosition('cell_2_1')) : ?>
            <td><?php echo $this->renderPosition('cell_2_1', ['style' => 'filter.block']); ?></td>
        <?php endif; ?>
        <?php if ($this->checkPosition('cell_2_2')) : ?>
            <td><?php echo $this->renderPosition('cell_2_2', ['style' => 'filter.block']); ?></td>
        <?php endif; ?>
        <?php if ($this->checkPosition('cell_2_3')) : ?>
            <td><?php echo $this->renderPosition('cell_2_3', ['style' => 'filter.block']); ?></td>
        <?php endif; ?>
        <?php if ($this->checkPosition('cell_2_4')) : ?>
            <td><?php echo $this->renderPosition('cell_2_4', ['style' => 'filter.block']); ?></td>
        <?php endif; ?>
    </tr>
</table>
