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
<tbody>
<?php
$i = 0;
foreach ($items as $key => $item) :
    $image = null;

    if ($path = $item->find('elements._image')) {
        $path = $this->app->jbimage->getUrl($path);

        $file = $this->clean(basename($path));
        $name = $this->clean($item->get('name'));

        $cid = $this->clean($key) . '-' . $file;
        $cid = JString::str_ireplace(' ', '', $cid);
        $src = 'cid:' . $cid;

        $image = '<img width="50" src="' . $src . '" title="' . ucfirst($name) . '"/>';
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
            <?php echo JBCart::val($item->find('elements._value'))->html(); ?>
        </td>
        <td align="center">
            <?php echo $item->get('quantity'); ?>
        </td>
        <td align="right">
            <?php echo $total->html(); ?>
        </td>
    </tr>

<?php endforeach; ?>
</tbody>