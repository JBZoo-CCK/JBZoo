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

$list = $this->_getTypeList();

echo $this->app->jbhtml->dataList(array(
    'JBZOO_ELEMENT_SHIPPING_NEWPOST_DELIVERYTYPE_ID_VALUE' => $list[$this->get('deliveryType_id')],
    'JBZOO_ELEMENT_SHIPPING_EMSPOST_REGION_VALUE'          => $this->get('region'),
    'JBZOO_ELEMENT_SHIPPING_EMSPOST_RECIPIENTCITY_VALUE'   => $this->get('recipientCity'),
    'JBZOO_ELEMENT_SHIPPING_EMSPOST_WAREHOUSE_VALUE'       => $this->get('warehouse'),
    'JBZOO_ELEMENT_SHIPPING_EMSPOST_STREET_VALUE'          => $this->get('street'),
    'JBZOO_ELEMENT_SHIPPING_EMSPOST_FLOOR_COUNT_VALUE'     => $this->get('floor_count'),
));
