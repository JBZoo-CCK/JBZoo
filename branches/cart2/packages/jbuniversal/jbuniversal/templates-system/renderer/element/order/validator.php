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

// create class attribute
$classes = array_filter(array(
    'jbcart-validator-row',
    'jbcart-validator-' . $element->getElementType(),
    $params->get('first') ? 'first' : '',
    $params->get('last') ? 'last' : '',
    $params->get('required') ? 'required' : '',
));

$element->loadAssets();

?>

<div class="<?php echo implode(' ', $classes); ?>">
    <div class="jbcart-validator-message">
        <?php echo $element->render($params); ?>
    </div>

    <?php if ($description) : ?>
        <div class="jbcart-validator-desc"><?php echo $description; ?></div>
    <?php endif; ?>
</div>
