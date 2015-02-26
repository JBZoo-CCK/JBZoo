<?php
/**
 * JBZoo App is universal Joomla CCK, application for YooTheme Zoo component
 * @package     jbzoo
 * @version     2.x Pro
 * @author      JBZoo App http://jbzoo.com
 * @copyright   Copyright (C) JBZoo.com,  All rights reserved.
 * @license     http://jbzoo.com/license-pro.php JBZoo Licence
 * @coder       Alexander Oganov <t_tapak@yahoo.com>
 */

// no direct access
defined('_JEXEC') or die('Restricted access'); ?>

<td class="sku-td">
    <?php if ($this->checkPosition('cell1')) :
        echo $this->renderPosition('cell1');
    endif; ?>
</td>

<td>
    <?php if ($this->checkPosition('cell2')) :
        echo $this->renderPosition('cell2');
    endif; ?>
</td>

<td>
    <?php if ($this->checkPosition('cell3')) :
        echo $this->renderPosition('cell3');
    endif; ?>
</td>

<td>
    <?php if ($this->checkPosition('cell4')) :
        echo $this->renderPosition('cell4');
    endif; ?>
</td>

<td>
    <?php if ($this->checkPosition('cell5')) :
        echo $this->renderPosition('cell5');
    endif; ?>
</td>

<td class="buttons-td">
    <?php if ($this->checkPosition('cell6')) :
        echo $this->renderPosition('cell6');
    endif; ?>
</td>