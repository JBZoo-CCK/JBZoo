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


?>
<table class="filter-table">
    <tr>
        <td style="width:25%;"><?php echo $this->renderPosition('cell_1_1', array('style' => 'filter.block')); ?></td>
        <td style="width:25%;"><?php echo $this->renderPosition('cell_1_2', array('style' => 'filter.block')); ?></td>
        <td style="width:25%;"><?php echo $this->renderPosition('cell_1_3', array('style' => 'filter.block')); ?></td>
        <td style="width:25%;"><?php echo $this->renderPosition('cell_1_4', array('style' => 'filter.block')); ?></td>
    </tr>
    <tr>
        <td style="width:25%;"><?php echo $this->renderPosition('cell_2_1', array('style' => 'filter.block')); ?></td>
        <td style="width:25%;"><?php echo $this->renderPosition('cell_2_2', array('style' => 'filter.block')); ?></td>
        <td style="width:25%;"><?php echo $this->renderPosition('cell_2_3', array('style' => 'filter.block')); ?></td>
        <td style="width:25%;" class="controls">
            <input type="submit" name="submit" value="<?php echo JText::_('JBZOO_BUTTON_SUBMIT'); ?>"
                   class="jsSubmit button rborder"/>
        </td>
    </tr>
</table>

