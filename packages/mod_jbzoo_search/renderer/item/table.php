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


?>
<table class="jbfilter-table no-border">
    <tr>
        <?php if ($this->checkPosition('cell_1_1')) : ?>
            <td><?php echo $this->renderPosition('cell_1_1', array('style' => 'filter.block')); ?></td>
        <?php endif; ?>
        <?php if ($this->checkPosition('cell_1_2')) : ?>
            <td><?php echo $this->renderPosition('cell_1_2', array('style' => 'filter.block')); ?></td>
        <?php endif; ?>
        <?php if ($this->checkPosition('cell_1_3')) : ?>
            <td><?php echo $this->renderPosition('cell_1_3', array('style' => 'filter.block')); ?></td>
        <?php endif; ?>
        <?php if ($this->checkPosition('cell_1_4')) : ?>
            <td><?php echo $this->renderPosition('cell_1_4', array('style' => 'filter.block')); ?></td>
        <?php endif; ?>
    </tr>
    <tr>
        <?php if ($this->checkPosition('cell_2_1')) : ?>
            <td><?php echo $this->renderPosition('cell_2_1', array('style' => 'filter.block')); ?></td>
        <?php endif; ?>
        <?php if ($this->checkPosition('cell_2_2')) : ?>
            <td><?php echo $this->renderPosition('cell_2_2', array('style' => 'filter.block')); ?></td>
        <?php endif; ?>
        <?php if ($this->checkPosition('cell_2_3')) : ?>
            <td><?php echo $this->renderPosition('cell_2_3', array('style' => 'filter.block')); ?></td>
        <?php endif; ?>
        <?php if ($this->checkPosition('cell_2_4')) : ?>
            <td><?php echo $this->renderPosition('cell_2_4', array('style' => 'filter.block')); ?></td>
        <?php endif; ?>
    </tr>
</table>
