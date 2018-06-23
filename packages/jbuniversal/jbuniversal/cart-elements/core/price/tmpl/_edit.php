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

if (($html === '' || $html === null) && (!$this->isCore())) {
    $html = $this->getJBPrice()->renderWarning('_warning.php', JText::_('JBZOO_PRICE_EDIT_ERROR_ADD_OPTIONS'));
}

$type   = $this->getElementType();
$isCore = ($this->isCore() ? 'core' : 'simple');
$attr   = array(
    'class' => array(
        'jbprice-element',
        'jsElement',
        'js' . JString::ucfirst($type),
        'js' . JString::ucfirst($isCore)
    )
);

echo '<div ' . $this->_attrs($attr) . '>' . $html . '</div>';