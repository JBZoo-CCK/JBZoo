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
$showlabel = (int)$params->get('showlabel', 0);

$html = array();
if ($showlabel) {
    $html[] = '<tr><td align="left" valign="top">';
    $html[] = '<h3 style="color: #444444;margin: 0 0 15px 0;font-size: 18px;">';
    $html[] = $element->getName();
    $html[] = '</h3>';
    $html[] = $element->render($params);
    $html[] = '</td></tr>';
} else {
    $html[] = '<tr><td align="left" valign="top">';
    $html[] = $element->render($params);
    $html[] = '</td></tr>';
}

// render result
echo implode(PHP_EOL, $html);
