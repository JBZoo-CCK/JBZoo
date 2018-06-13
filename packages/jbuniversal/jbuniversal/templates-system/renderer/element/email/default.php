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

// check label
$label = (int)$params->get('showlabel') ? '<h4>' . $element->getName() . '</h4> ' : null;

// render HTML for current element
$render = $element->render($params);

// render result
if (!is_null($render)) {
    echo $label . $render;
}
