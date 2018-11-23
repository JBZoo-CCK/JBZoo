<?php
/**
 * JBZoo Application
 *
 * This file is part of the JBZoo CCK package.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package    Application
 * @license    GPL-2.0
 * @copyright  Copyright (C) JBZoo.com, All rights reserved.
 * @link       https://github.com/JBZoo/JBZoo
 * @author     Denis Smetannikov <denis@jbzoo.com>
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

$params = $this->app->data->create($params);

// add tooltip
$tooltip = '';
if ($params->get('show_tooltip') && ($description = $element->config->get('description'))) {
    $tooltip = ' class="hasTip" title="' . $description . '"';
}

// create error
$error   = '';
$isError = isset($element->error) && !empty($element->error);
if ($isError) {
    $error = '<p class="jbcart-shippingfield-error">' . (string)$element->error . '</p>';
}

// create class attribute
$classes = array_filter(array(
    'jsShippingField',
    'jbcart-shippingfield-row',
    'jbcart-shippingfield-' . $element->getElementType(),
    'js' . $element->identifier,
    $params->get('first') ? 'first' : '',
    $params->get('last') ? 'last' : '',
    $params->get('required') ? 'required' : '',
    $isError ? 'error' : '',
));

$element->loadAssets();

$label = $params->get('altlabel') ? $params->get('altlabel') : $element->getName();
$label = $params->get('required') ? ($label . ' <span class="required-dot">*</span>') : $label;

$uniqId = $element->htmlId();

?>
<div class="<?php echo implode(' ', $classes); ?>">

    <label class="jbcart-shippingfield-label" for="<?php echo $uniqId; ?>">
        <?php echo $label; ?>
    </label>

    <div class="jbcart-shippingfield-element">

        <?php if ($description = $element->config->get('description')) : ?>
            <p class="jbcart-shippingfield-desc"><?php echo JText::_($description); ?> </p>
        <?php endif; ?>

        <?php echo $element->renderSubmission($params); ?>

        <?php echo $error; ?>
    </div>

</div>
