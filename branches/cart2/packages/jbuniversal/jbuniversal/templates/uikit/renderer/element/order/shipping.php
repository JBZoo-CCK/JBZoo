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
    'jsShippingElement',
    'element-' . $element->getElementType(),
    $params->get('first') ? ' first' : '',
    $params->get('last') ? ' last' : '',
    $params->get('required') ? ' required-field' : '',
    $isError ? ' error' : '',
));

$element->loadAssets();

?>
<div data-type="<?php echo $element->getElementType(); ?>"
     data-settings='<?php echo $element->getWidgetParams(true); ?>'
     class="<?php echo implode(' ', $classes); ?>">
    <?php echo '<div class="shipping-element"> ' . $element->renderSubmission($params) . $error . ' </div>'; ?>
    <div class="clear"></div>
</div>
