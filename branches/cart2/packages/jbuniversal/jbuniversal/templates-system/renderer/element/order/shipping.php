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

// create error
$error   = '';
$isError = isset($element->error) && !empty($element->error);
if ($isError) {
    $error = '<p class="jbcart-shipping-error">' . (string)$element->error . '</p>';
}

// create class attribute
$classes = array_filter(array(
    'jbcart-shipping-row',
    'jbcart-shipping-' . $element->getElementType(),
    $params->get('first') ? 'first' : '',
    $params->get('last') ? 'last' : '',
    $isError ? 'error' : '',
));

$element->loadAssets();
$label  = $params->get('altlabel') ? $params->get('altlabel') : $element->config->get('name');
$uniqId = 'jbcart-' . $element->identifier;

?>

<div class="<?php echo implode(' ', $classes); ?>">

    <input <?php echo $this->app->jbhtml->buildAttrs(array(
        'type'    => 'radio',
        'id'      => $uniqId,
        'value'   => $element->identifier,
        'class'   => 'jbcart-radio-input jbcart-shipping-radio',
        'name'    => $element->getControlName('_shipping_id'),
        'checked' => $element->isDefault() ? 'checked' : '',
    ));?> />

    <label class="jbcart-radio-label jbcart-shipping-label" for="<?php echo $uniqId; ?>">

        <div class="jbcart-radio"></div>

        <div class="jbcart-shipping-name">
            <?php echo $label; ?>
            <span class="jbcart-shipping-price jsShippingPrice">
                 (<?php echo $element->getRate(); ?> )
            </span>
        </div>
        <div class="jbcart-shipping-element"><?php echo $element->renderSubmission($params); ?></div>

        <?php if ($description = $element->config->get('description')) : ?>
            <p class="jbcart-shipping-desc"><?php echo JText::_($description); ?> </p>
        <?php endif; ?>

        <?php echo $error; ?>
    </label>

</div>
