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
