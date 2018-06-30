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

$label = '';
if (isset($params['showlabel']) && $params['showlabel']) {
    $label = ($params['altlabel']) ? $params['altlabel'] : $element->getConfig()->get('name');
}

// render result HTML
echo '<param name="' . $this->app->jbyml->replaceSpecial($label) . '">'
    . $this->app->jbyml->replaceSpecial($element->getSearchData())
    . '</param>'
    . PHP_EOL;