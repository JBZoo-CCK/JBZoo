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
    'robokassa-payment',
    'jbzoo-payment',
    'element-' . $element->getElementType(),
    $params->get('first') ? ' first' : '',
    $params->get('last') ? ' last' : '',
    $params->get('required') ? ' required-field' : '',
    $isError ? ' error' : '',
));


$element->loadAssets();

?>
<div class="<?php echo implode(' ', $classes); ?>">
    <div class="jbzoo-payment-content">

        <?php echo '<input type="radio" '
            . 'name="' . $element->getControlName('_payment_id') . '" '
            . 'id="payment-' . $element->identifier . '" '
            . 'value="' . $element->identifier . '" '
            . 'class="payment-choose" '
            . ($element->isDefault() ? 'checked="checked" ' : '') . ' />';?>

        <label class="payment-label" for="<?php echo $element->identifier; ?>"> </label>

        <?php if ($description = $element->config->get('description')) {
            echo '<span class="jbzoo-payment-desc">' . $description . '</p>';
        }
        ?>

        <?php if ($element->isDebug()) : ?>
            <p class="debug-mode"><?php echo JText::_('JBZOO_PAYMENT_DEBUG_MESSAGE'); ?></p>
        <?php endif; ?>

    </div>
    <?

    // echo '<label class="field-label" for="payment-' . $element->identifier . '"> '
    // echo $element->getName();
    // echo '</label>';
    // echo '<div class="payment-element"> ' . $element->renderSubmission($params) . $error . ' </div>';

    ?>
</div>
