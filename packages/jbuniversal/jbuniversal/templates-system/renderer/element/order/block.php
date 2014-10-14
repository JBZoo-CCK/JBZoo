<?php
/**
 * JBZoo App is universal Joomla CCK, application for YooTheme Zoo component
 *
 * @package     jbzoo
 * @version     2.x Pro
 * @author      JBZoo App http://jbzoo.com
 * @copyright   Copyright (C) JBZoo.com,  All rights reserved.
 * @license     http://jbzoo.com/license-pro.php JBZoo Licence
 * @coder       Denis Smetannikov <denis@jbzoo.com>
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

$description = $element->config->get('description');
$params = $this->app->data->create($params);

// add tooltip
$tooltip = '';
if ($params->get('show_tooltip') && ($description)) {
    $tooltip = ' class="hasTip" title="' . $description . '"';
}

// create error
$error = '';
$isError = isset($element->error) && !empty($element->error);
if ($isError) {
    $error = '<p class="error-message">' . (string)$element->error . '</p>';
}

// create class attribute
$classes = array_filter(array(
    'control-group',
    'element',
    'element-' . $element->getElementType(),
    $params->get('first') ? ' first' : '',
    $params->get('last') ? ' last' : '',
    $params->get('required') ? ' required-field' : '',
    $isError ? ' error' : '',
));

$element->loadAssets();

$label = $params->get('altlabel') ? $params->get('altlabel') : $element->config->get('name');
$label = $params->get('required') ? ($label . ' <span class="dot">*</span>') : $label;

?>
<div class="<?php echo implode(' ', $classes); ?>">
    <?php
    echo '<div class="control-label">'
         . '<label class="field-label" for="order-' . $element->identifier . '">'
         . $label
         . '</label></div>';

    echo '<div class="controls"> '
         . $element->renderSubmission($params)
         . $error
         . '</div>';

    echo '<div class="description">' . $description . '</div>'; ?>
    
    <div class="clear"></div>
</div>
