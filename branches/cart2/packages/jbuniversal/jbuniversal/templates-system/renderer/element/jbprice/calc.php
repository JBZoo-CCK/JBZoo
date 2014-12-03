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

$label = ($params['altlabel']) ? $params['altlabel'] : $element->config->get('name');
$desc  = $element->getDescription();

$this->app->jbassets->initTooltip();

$classes = array(
    'element-' . $element->getType(),
);

?>

<tr class="<?php echo implode(' ', $classes); ?>">

    <td class="clac-label">
        <strong>
            <?php echo $label; ?>
            <?php if ($desc) : ?>
                <span class="jbtooltip" title="<?php echo $desc; ?>"> </span>
            <?php endif; ?>
        </strong>
    </td>

    <td class="clac-field">
        <?php echo $element->render($params); ?>
    </td>
</tr>
