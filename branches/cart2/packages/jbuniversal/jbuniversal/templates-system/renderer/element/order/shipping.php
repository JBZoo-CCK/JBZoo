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
$label  = $params->get('altlabel') ? $params->get('altlabel') : $element->getName();
$uniqId = $element->htmlId();

$exceptionMessage = '';
try {
    $rate = $element->getRate();
} catch (JBCartElementShippingException $e) {
    $exceptionMessage = $e->getMessage();
}
?>

<div class="<?php echo implode(' ', $classes); ?>" data-type="<?php echo $element->getElementType(); ?>">

    <input <?php echo $this->app->jbhtml->buildAttrs(array(
        'type'  => 'hidden',
        'value' => $element->identifier,
        'name'  => $element->getControlName('element_id'),
    )); ?> />

    <input <?php echo $this->app->jbhtml->buildAttrs(array(
        'type'    => 'radio',
        'id'      => $uniqId,
        'value'   => $element->identifier,
        'name'    => 'shipping[_shipping_id]',
        'checked' => $element->isDefault() ? 'checked' : '',
        'class'   => array(
            'jsRadio',
            'jbcart-radio-input',
            'jbcart-shipping-radio',
        ),
    )); ?> />

    <div class="jbcart-radio-label jbcart-shipping-wrapper">

        <label for="<?php echo $uniqId; ?>">
            <div class="jbcart-radio"></div>
        </label>

        <label class="jbcart-shipping-name" for="<?php echo $uniqId; ?>">
            <?php echo $label; ?>
            <span class="jbcart-shipping-price jsShippingElementPrice">
                (<span class="jsPrice-<?php echo $element->identifier; ?>">
                    <?php echo $rate->html(); ?>
                </span>)
            </span>
        </label>

        <?php
        $description       = $element->config->get('description');
        $elementSubmission = $element->renderSubmission($params);

        if ($elementSubmission || $description) {

            $html = array();

            $html[] = '<div class="jbcart-shipping-element jsShippingElement jsShippingAjax-' . $element->identifier . '">';

            if ($description) {
                $html[] = '<p class="jbcart-shipping-desc">' . JText::_($description) . '</p>';
            }

            $html[] = $elementSubmission;
            $html[] = $error;

            $html[] = '<div class="jbcart-shipping-exception jsPrice-' . $element->identifier . '-exception">'
                . $exceptionMessage
                . '</div>';

            $html[] = '</div>';

            echo implode(PHP_EOL, $html);
        } ?>
    </div>

</div>
