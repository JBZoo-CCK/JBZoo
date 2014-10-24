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

?>
<table <?php echo $this->getAttrs(array(
        'width'       => '100%',
        'bgcolor'     => '#fafafa',
        'cellspacing' => 0,
        'cellpadding' => 10
    )) .
    $this->getStyles(array(
        'border'        => '1px solid #dddddd',
        'border-radius' => '4px',
        'margin-top'    => '35px'
    ));?>
    >
    <tr>
        <td <?php echo $this->getAttrs(array(
            'colspan' => 2
        ));?>
            >
            <h3 style="color: #444444;margin: 0 0 15px 0;font-size: 18px;">
                <?php echo $title; ?>
            </h3>
        </td>
    </tr>

    <tr>
        <td align="left">
            <strong>
                <?php echo JText::_('JBZOO_ORDER_SHIPPING_NAME'); ?>
            </strong>
        </td>

        <td align="left">
            <?php echo $shipping->getName(); ?>
        </td>
    </tr>

    <tr>
        <td align="left">
            <strong>
                <?php echo JText::_('JBZOO_ORDER_SHIPPING_PRICE'); ?>
            </strong>
        </td>

        <td align="left">
            <?php echo $this->_jbmoney->toFormat($data->get('value', 0)); ?>
        </td>
    </tr>
    <?php if ($fields = $data->get('fields')) :
        foreach ($fields as $key => $field) :
            $name = JText::_('JBZOO_ORDER_SHIPPING_' . $shipping->getElementType() . '_' . $key);
            ?>
            <tr>
                <td align="left">
                    <strong>
                        <?php echo $name; ?>
                    </strong>
                </td>

                <td align="left">
                    <?php echo $field; ?>
                </td>
            </tr>

        <?php endforeach;
    endif;

    if (!empty($fieldParams)) :

        echo $this->partial('shippingfield', array(
                'data'  => $fieldParams->get('data'),
                'title' => $fieldParams->get('title')
            )
        );
    endif; ?>
</table>


