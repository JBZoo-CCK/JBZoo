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

$type   = $this->getElementType();
$isCore = ($this->isCore() ? 'core' : 'simple');

$attr = array(
    'data-identifier' => $this->identifier,
    'data-template'   => $this->template,
    'data-index'      => $this->index,
    'data-position'   => $this->position,
    'class'           => array(
        'jbprice-' . $type, // very IMPORTANT class for element templates (DON'T REMOVE!)
        'jsElement',
        'jsPriceElement',
        'js' . ucfirst($type),
        'js' . ucfirst($isCore),
        'jselement'   . strtolower($this->template . $this->position . $this->index)
    )
);

echo '<div ' . $this->_attrs($attr) . '>' . $html . '</div>';
