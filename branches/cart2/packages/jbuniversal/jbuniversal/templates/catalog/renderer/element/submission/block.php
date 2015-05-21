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
    $tooltip = ' class="hasTip" title="' . JText::_($description) . '"';
}

// create label
$label = '<strong' . $tooltip . '>';
$label .= $params->get('altlabel') ? $params->get('altlabel') : $element->config->get('name');
$label .= $params->get('required') ? ' <span class="required-dot">*</span>' : '';
$label .= '</strong>';

// create error
$error   = '';
$isError = isset($element->error) && !empty($element->error);
if ($isError) {
    $error = '<p class="error-message">' . (string)$element->error . '</p>';
}

// create class attribute
$classes = array_filter(array(
    'element',
    'element-' . $element->getElementType(),
    $params->get('first') ? 'first' : '',
    $params->get('last') ? 'last' : '',
    $params->get('required') ? 'required' : '',
    $isError ? 'error' : '',
));

$element->loadAssets();

?>
<div class="<?php echo implode(' ', $classes); ?>">
    <?php echo $label . $element->renderSubmission($params) . $error; ?>
</div>
