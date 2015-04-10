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

// assets
$this->app->jbassets->less('jbassets:less/general.less');
$this->app->jbassets->initTooltip();

$isShowlabel = (int)$params->get('showlabel');
if ($isShowlabel) {
    $label = ($params['altlabel']) ? $params['altlabel'] : $element->getName();
}

$attrs = array(
    'class' => array(
        'element-' . $element->identifier,
        'element-' . $element->getElementType(),
        $params['first'] ? 'first' : '',
        $params['last'] ? 'last' : '',
    )
);

?>

<tr <?php echo $this->app->jbhtml->buildAttrs($attrs); ?>>

    <?php if ($isShowlabel) : ?>
        <td class="element-label">
            <?php echo $label; ?>

            <?php if ($description = $element->getDescription()) : ?>
                <span class="jbtooltip" title="<?php echo $description; ?>"> </span>
            <?php endif; ?>

        </td>
    <?php endif; ?>

    <td class="element-body" <?php if (!$isShowlabel) : ?>colspan="2"<?php endif; ?>>
        <?php echo $element->render($params); ?>
    </td>
</tr>
