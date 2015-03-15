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
defined('_JEXEC') or die('Restricted access');

if (!empty($items)) : ?>

    <table <?php echo $this->getAttrs(array(
            'width'       => '100%',
            'cellpadding' => 10,
            'bgcolor'     => '#fafafa',
            'frame'       => 'box'
        )) .
        $this->getStyles(array(
            'border'        => '1px solid #dddddd',
            'border-radius' => '4px',
            'margin-top'    => '35px'
        )); ?>
        >
        <tr>
            <td>
                <strong>
                    <?php echo $this->getName(); ?>
                </strong>
            </td>
        </tr>
        <?php foreach ($items as $data) {
            $item = $data->get('item');
            if (($item instanceof Item) && ($element = $item->getElement($identifier))) {
                $query = array(
                    'task'    => 'callelement',
                    'format'  => 'raw',
                    'item_id' => $data->get('item_id'),
                    'element' => $element->identifier,
                    'method'  => 'download'
                );

                if($file = $element->get('file')) {
                    echo $this->partial('item', array(
                        'link'          => JUri::root() . ltrim($this->app->link($query), "\\/"),
                        'size'          => isset($size) ? $element->getSize() : null,
                        'download_name' => $data->get('item_name'),
                        'filename'      => $this->filename($file)
                    ));
                }
            }
        } ?>
    </table>
<?php endif;
