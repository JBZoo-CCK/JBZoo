<?php
/**
 * JBZoo App is universal Joomla CCK, application for YooTheme Zoo component
 *
 * @package     jbzoo
 * @version     2.x Pro
 * @author      JBZoo App http://jbzoo.com
 * @copyright   Copyright (C) JBZoo.com,  All rights reserved.
 * @license     http://jbzoo.com/license-pro.php JBZoo Licence
 * @coder       Alexander Oganov <t_tapak@yahoo.com>
 */

// no direct access
defined('_JEXEC') or die('Restricted access'); ?>

<tr>
    <td align="left">
        <strong>
            <?php echo $download_name; ?>
        </strong>
    </td>
    <td align="left">
        <?php echo '<a href="' . $link . '" title="' . $filename . '">'
            . $filename
            . '</a>'
            . str_repeat('&nbsp;', 3)
            . '<span style="font-size: 13px;"><i>'
            . $size
            . '</i></span>'; ?>
    </td>
</tr>
