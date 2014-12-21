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
    'jsShipping',
    'jbcart-shipping-row',
    'jbcart-shipping-' . $element->getElementType(),
    $element->isDefault() ? 'active' : '',
    $params->get('first') ? 'first' : '',
    $params->get('last') ? 'last' : '',
    $isError ? 'error' : '',
));

$element->loadAssets();
$label  = $params->get('altlabel') ? $params->get('altlabel') : $element->config->get('name');
$uniqId = 'jbcart-' . $element->identifier;

?>

<div class="<?php echo implode(' ', $classes); ?>" data-type="<?php echo $element->getElementType(); ?>">

    <input <?php echo $this->app->jbhtml->buildAttrs(array(
        'type'    => 'radio',
        'id'      => $uniqId,
        'value'   => $element->identifier,
        'class'   => array(
            'jsRadio',
            'jbcart-radio-input',
            'jbcart-shipping-radio',
        ),
        'name'    => $element->getControlName('_shipping_id'),
        'checked' => $element->isDefault() ? 'checked' : '',
    ));?> />

    <div class="jbcart-radio-label jbcart-shipping-wrapper">

        <div class="jbcart-radio"></div>

        <label class="jbcart-shipping-name" for="<?php echo $uniqId; ?>">
            <?php echo $label; ?>
            <span class="jbcart-shipping-price jsShippingElementPrice">
                (<span class="jsPrice-<?php echo $element->identifier; ?>">
                    <?php echo $element->getRate()->html(); ?>
                </span>)
            </span>
        </label>

        <?php
        $description = $element->config->get('description');
        $element     = $element->renderSubmission($params);

        if ($element || $description) {

            $html = array();

            $html[] = '<div class="jbcart-shipping-element jsShippingElement">';

            if ($description) {
                $html[] = '<p class="jbcart-shipping-desc">' . JText::_($description) . '</p>';
            }

            $html[] = $element;
            $html[] = $error;

            $html[] = '</div>';

            echo implode("\n", $html);
        } ?>
    </div>

</div>
