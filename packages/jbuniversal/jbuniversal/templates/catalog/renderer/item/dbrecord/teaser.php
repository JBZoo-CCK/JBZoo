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

<tr class="table-row item_<?php echo $item->id; ?>">
    <td><?php echo $this->renderPosition('cell1'); ?></td>
    <td><?php
        if ($html = $this->renderPosition('cell2')) {
            echo $html;
        } else {
            echo ' - ';
        }
        ?></td>
    <td><?php echo $this->renderPosition('cell3'); ?></td>
    <td><?php echo $this->renderPosition('cell4'); ?></td>
    <td><?php echo $this->renderPosition('cell5'); ?></td>
    <td><?php echo $this->renderPosition('cell6'); ?></td>
    <td><?php echo $this->renderPosition('cell7'); ?></td>
    <td><?php echo $this->renderPosition('cell8'); ?></td>
</tr>