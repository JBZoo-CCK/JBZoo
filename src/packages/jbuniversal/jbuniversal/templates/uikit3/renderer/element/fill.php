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

$type = strtoupper($params['type']);
$elId = $this->app->jbstring->getId('basic-');

$labelText = ($params['altlabel']) ? $params['altlabel'] : $element->getConfig()->get('name');
// render HTML for  current element
$render = $element->render($params);
$labelText = strip_tags($labelText);
?>


<div class="uk-grid-small" uk-grid>
    <div class="uk-width-expand" uk-leader="fill: -"><?php echo $labelText ?></div>
    <div><?php echo $render ?></div>
</div>

