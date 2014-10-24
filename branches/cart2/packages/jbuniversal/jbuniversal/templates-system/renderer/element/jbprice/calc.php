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

?>

<tr class="element-<?php echo $element->getType(); ?>">

    <td class="clac-label">
        <label>
            <?php echo $label; ?>
            <?php if ($desc) : ?>
                <span class="jbtooltip" title="1<?php echo $desc; ?>"> </span>
            <?php endif; ?>
        </label>
    </td>

    <td class="clac-field">
        <?php echo $element->render($params); ?>
    </td>
</tr>
