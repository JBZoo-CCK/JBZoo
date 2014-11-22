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
if ($element->isDebug()) {
    $error .= '<p class="payment-debug">' . JText::_('JBZOO_PAYMENT_DEBUG_MESSAGE') . '</p>';
}

$isError = isset($element->error) && !empty($element->error);
if ($isError) {
    $error .= '<p class="payment-error">' . (string)$element->error . '</p>';
}

// create class attribute
$classes = array_filter(array(
    'element-payment',
    'robokassa-payment',
    'jbzoo-payment',
    'element-' . $element->getElementType(),
    $params->get('first') ? ' first' : '',
    $params->get('last') ? ' last' : '',
    $params->get('required') ? ' required-field' : '',
    $isError ? ' error' : '',
));

$element->loadAssets();
$html = $element->renderSubmission($params);
if (empty($html)) {
    $html = $element->getName();
}

$paymentId = 'payment-' . $element->identifier;
?>
<div class="<?php echo implode(' ', $classes); ?>">

    <input type="radio" <?php echo($element->isDefault() ? 'checked="checked" ' : ''); ?>
           name="<?php echo $element->getControlName('_payment_id'); ?>"
           value="<?php echo $element->identifier; ?>"
           id="<?php echo $paymentId; ?>"
           class="payment-choose ghost"
        <?php echo $element->isDefault() ? 'checked="checked"' : '' ?>
        />

    <label class="ghost-label payment-label jbzoo-payment-content" for="<?php echo $paymentId; ?>">

        <div class="jbradio"></div>

        <div class="payment-element">
            <?php echo $html; ?>
        </div>

        <?php if ($description = $element->config->get('description')) : ?>
            <p class="jbzoo-payment-desc"><?php echo JText::_($description); ?> </p>
        <?php endif; ?>

        <?php echo $error; ?>

    </label>

</div>
