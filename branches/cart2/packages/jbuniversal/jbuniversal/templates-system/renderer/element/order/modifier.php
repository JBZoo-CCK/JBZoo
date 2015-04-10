<?php
/**
 * JBZoo App is universal Joomla CCK, application for YooTheme Zoo component
 * @package     jbzoo
 * @version     2.x Pro
 * @author      JBZoo App http://jbzoo.com
 * @copyright   Copyright (C) JBZoo.com,  All rights reserved.
 * @license     http://jbzoo.com/license-pro.php JBZoo Licence
 * @coder       Denis Smetannikov <denis@jbzoo.com>
 */

// no direct access
defined('_JEXEC') or die('Restricted access');


$params = $this->app->data->create($params);

// add tooltip
$tooltip = '';
if ($params->get('show_tooltip') && ($description = $element->config->get('description'))) {
    $tooltip = ' class="hasTip" title="' . $description . '"';
}

// create error
$error   = '';
$isError = isset($element->error) && !empty($element->error);
if ($isError) {
    $error = '<p class="jbcart-modifier-error">' . (string)$element->error . '</p>';
}

// create class attribute
$classes = array_filter(array(
    'jsModifier',
    'jbcart-modifier-row',
    'jbcart-modifier-' . $element->getElementType(),
    $params->get('first') ? 'first' : '',
    $params->get('last') ? 'last' : '',
    $params->get('required') ? 'required' : '',
    $isError ? 'error' : '',
));

$element->loadAssets();

$label = $params->get('altlabel') ? $params->get('altlabel') : $element->getName();
$label = $params->get('required') ? ($label . ' <span class="required-dot">*</span>') : $label;

$uniqId = $element->htmlId();

?>
<tr class="<?php echo implode(' ', $classes); ?>">

    <td class="jbcart-cell-empty"></td>

    <td colspan="2" class="jbcart-modifier-label">
        <label for="<?php echo $uniqId; ?>"><?php echo $label; ?></label>
        <?php if ($description = $element->config->get('description')) : ?>
            <p class="jbcart-modifier-desc"><?php echo JText::_($description); ?> </p>
        <?php endif; ?>
    </td>

    <td colspan="3" class="jbcart-modifier-element jsModifier-<?php echo $element->identifier; ?>">
        <?php echo $element->renderSubmission($params); ?>
        <?php echo $error; ?>
    </td>

</tr>
