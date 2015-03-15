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

$description = $element->config->get('description');
$params      = $this->app->data->create($params);

// add tooltip
$tooltip = '';
if ($params->get('show_tooltip') && ($description)) {
    $tooltip = ' class="hasTip" title="' . $description . '"';
}

// create error
$error   = '';
$isError = isset($element->error) && !empty($element->error);
if ($isError) {
    $error = '<p class="jbcart-form-error">' . (string)$element->error . '</p>';
}

// create class attribute
$classes = array_filter(array(
    'jbcart-form-row',
    'jbcart-form-' . $element->getElementType(),
    $params->get('first') ? 'first' : '',
    $params->get('last') ? 'last' : '',
    $params->get('required') ? 'required' : '',
    $isError ? 'error' : '',
));

$element->loadAssets();

$label = $params->get('altlabel') ? $params->get('altlabel') : $element->getName();
$label = $params->get('required') ? ($label . ' <span class="required-dot">*</span>') : $label;

?>

<div class="<?php echo implode(' ', $classes); ?>">

    <label class="jbcart-form-label" for="<?php echo $element->htmlId(); ?>">
        <?php echo $label; ?>
    </label>

    <div class="jbcart-form-control">
        <?php echo $element->renderSubmission($params); ?>
        <?php echo $error; ?>
    </div>

    <div class="jbcart-form-desc">
        <?php echo $description; ?>
    </div>

</div>
