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

$config    = $this->config;
$items     = $order->getItems();
$itemsHtml = $order->renderItems(array(
    'image_width'  => $this->config->get('tmpl_image_width', 75),
    'image_height' => $this->config->get('tmpl_image_height', 75),
    'image_link'   => $this->config->get('tmpl_image_link', 1),
    'item_link'    => $this->config->get('tmpl_item_link', 1),
    'currency'     => $this->_getCurrency(),
    'email'        => true,
));

$i = 0;
?>

<?php
foreach ($itemsHtml as $itemKey => $itemHtml) :
    $i++;

    $rowattr = 'style="border-bottom: 1px solid #dddddd;"';
    if ($i % 2 == 1) {
        $rowattr .= ' bgcolor="#fafafa"';
    }

    ?>
    <tr <?php echo $rowattr; ?>>

        <td><?php echo $i;?></td>

        <td>
            <?php if ($config->get('tmpl_image_show', 1)) {
                echo $itemHtml['image'];
                $imageEmail = $itemHtml['imageEmail'];

                if ($imageEmail['path']) { // attach as content-id image
                    $this->_addEmailImage($imageEmail['path'], $imageEmail['cid']);
                }

            } ?>
        </td>

        <td>
            <?php echo $itemHtml['name']; ?>
            <?php echo $config->get('tmpl_sku_show', 1) ? $itemHtml['sku'] : null;?>
            <?php echo $itemHtml['params']; ?>
        </td>

        <td>
            <?php echo $config->get('tmpl_price4one', 1) ? $itemHtml['price4one'] : null;?>
        </td>

        <td>
            <?php echo $config->get('tmpl_quntity', 1) ? $itemHtml['quantity'] : null;?>
        </td>

        <td>
            <?php echo $config->get('tmpl_subtotal', 1) ? $itemHtml['totalsum'] : null;?>
        </td>

    </tr>
<?php endforeach; ?>
