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


$params = $this->app->data->create($params);

// create error
$error = '';
$isError = isset($element->error) && !empty($element->error);
if ($isError) {
    $error = '<p class="error-message">' . (string)$element->error . '</p>';
}

// create class attribute
$classes = array_filter(array(
    'form-field-row',
    'element',
    'element-' . $element->getElementType(),
    $params->get('first') ? ' first' : '',
    $params->get('last') ? ' last' : '',
    $params->get('required') ? ' required-field' : '',
    $isError ? ' error' : '',
));

$element->loadAssets();

?>
<div class="<?php echo implode(' ', $classes); ?>">
    <?php

    echo '<label class="field-label" for="payment-' . $element->identifier . '"> '
        . '<input type="radio" name="' . $element->getControlName('_payment_id') . '" '
        . 'id="payment-' . $element->identifier . '" '
        . 'value="' . $element->identifier . '" '
        . ($element->isDefault() ? 'checked="checked" ' : '') . ' />';

    echo $element->getName();
    echo '</label>';

    if ($description = $element->config->get('description')) {
        echo '<p class="payment-description">' . $description . '</p>';
    }

    echo '<div class="payment-element"> ' . $element->renderSubmission($params) . $error . ' </div>';

    ?>
    <div class="clear"></div>
</div>
