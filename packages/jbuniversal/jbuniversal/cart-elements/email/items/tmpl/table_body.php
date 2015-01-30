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

$order = $this->getOrder();
$items = $order->getItems()
?>
<tbody>
<?php
$i = 0;

foreach ($items as $key => $item) :
    $image = null;

    $modifiers = $order->getModifiersItemPrice(null, $item);

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
        $rowattr .= ' bgcolor="#fafafa;"';
    }
    $itemPrice = $order->val((float)$item->get('total')); ?>
    <tr <?php echo $rowattr; ?>>
        <td><?php echo $i; ?></td>
        <td><?php echo $image; ?></td>
        <td>
            <?php echo $item['item_name'];?>

            <?php if (!empty($item['values'])) : ?>
                <ul style="margin:6px 0 0 0;padding:0;">
                    <?php foreach ($item['values'] as $label => $param) :
                        echo '<li style="list-style-type: none;"><strong>' . $label . ':</strong> ' . $param . '</li>';
                    endforeach; ?>
                </ul>
            <?php endif; ?>

            <?php if ($item->find('elements._description')) :
                echo '<p><i>' . $item->find('elements._description') . '</i></p>';
            endif; ?>
        </td>
        <td align="center"><?php echo $itemPrice->html(); ?></td>
        <td align="center"><?php echo $item->get('quantity'); ?></td>
        <td align="right"><?php echo $itemPrice->multiply($item->get('quantity'))->html();?></td>
    </tr>

<?php endforeach; ?>
</tbody>
