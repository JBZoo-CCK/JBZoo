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

$params = $this->app->data->create($params);

$showlabel = (int)$params->get('showlabel', 0);

// render HTML for current element
$render = $element->render($params);

// render result
if (is_null($render)) {
    return null;
}


?>
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

    <?php if ($showlabel) : ?>
        <tr>
            <td align="left" valign="top">
                <h3 style="color: #444444;margin: 0 0 15px 0;font-size: 18px;">
                    <?php echo $element->getName(); ?>
                </h3>
            </td>
        </tr>
    <?php endif; ?>

    <tr>
        <td align="left" valign="top">
            <?php echo $render; ?>
        </td>
    </tr>
</table>