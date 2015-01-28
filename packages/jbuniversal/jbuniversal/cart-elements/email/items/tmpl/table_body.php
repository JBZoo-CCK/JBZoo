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
defined('_JEXEC') or die('Restricted access');

$i = 0; ?>
<tbody>
<?php foreach ($items as $key => $item) :
    $image = null;
    if ($item->find('elements._image')) {
        $src   = 'cid:' . $this->clean($key) . '-' . $this->clean(basename($item->find('elements.image')));
        $src   = JString::str_ireplace(' ', '', $src);
        $image = '<img width="50" src="' . $src . '" title="' . $item['item_name'] . '"/>';
    }
    $i++;

    $rowattr = 'style="border-bottom: 1px solid #dddddd;"';
    if ($i % 2 == 1) {
        $rowattr .= ' bgcolor="#fafafa"';
    }

    $total = JBCart::val((float)$item->get('total'));
    $total->multiply($item->get('quantity')); ?>
    <tr <?php echo $rowattr; ?>>
        <td>
            <?php echo $i + 1; ?>
        </td>
        <td>
            <?php echo $image; ?>
        </td>
        <td>
            <?php echo $item['item_name']; ?>
        </td>
        <td align="center">
            <?php echo JBCart::val($item->find('elements._value')); ?>
        </td>
        <td align="center">
            <?php echo $item->get('quantity'); ?>
        </td>
        <td align="right">
            <?php echo $total; ?>
        </td>
    </tr>

<?php endforeach; ?>
</tbody>