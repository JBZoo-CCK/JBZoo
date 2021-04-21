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

$tarif = (int) $this->get('tariff', 136);
$type  = '';

if ($tarif == 136) {
    $type = JText::_('JBZOO_ELEMENT_SHIPPING_SDEK_EDIT_PVZ');
} elseif ($tarif == 137) {
    $type = JText::_('JBZOO_ELEMENT_SHIPPING_SDEK_EDIT_COURIER');
}

$fields = array(
    JText::_('JBZOO_ELEMENT_SHIPPING_SDEK_EDIT_METHOD') => $type,
    JText::_('JBZOO_ELEMENT_SHIPPING_SDEK_EDIT_CITY')   => $this->get('to')['city-name'],
);

if ($tarif == 136) {
    $fields[JText::_('JBZOO_ELEMENT_SHIPPING_SDEK_EDIT_ADDRESS_1')]  = $this->get('address', JText::_('JBZOO_ELEMENT_SHIPPING_SDEK_EDIT_NONE'));
}

if ($tarif == 137) {
    $fields[JText::_('JBZOO_ELEMENT_SHIPPING_SDEK_EDIT_ADDRESS_2')]  = $this->get('address', JText::_('JBZOO_ELEMENT_SHIPPING_SDEK_EDIT_NONE'));
}

echo $this->app->jbhtml->dataList($fields);