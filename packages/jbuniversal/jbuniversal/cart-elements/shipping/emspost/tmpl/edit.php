<?php
/**
 * JBZoo App is universal Joomla CCK, application for YooTheme Zoo component
 * @package     jbzoo
 * @version     2.x Pro
 * @author      JBZoo App http://jbzoo.com
 * @copyright   Copyright (C) JBZoo.com,  All rights reserved.
 * @license     http://jbzoo.com/license-pro.php JBZoo Licence
 * @coder       Alexander Oganov <t_tapak@yahoo.com>
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

echo $this->app->jbhtml->dataList(array(
    'JBZOO_ELEMENT_SHIPPING_EMSPOST_CITIES_VALUE'    => $this->getLocationName($this->get('cities', ' - ')),
    'JBZOO_ELEMENT_SHIPPING_EMSPOST_COUNTRIES_VALUE' => $this->getLocationName($this->get('countries', ' - ')),
    'JBZOO_ELEMENT_SHIPPING_EMSPOST_REGIONS_VALUE'   => $this->getLocationName($this->get('regions', ' - ')),
    'JBZOO_ELEMENT_SHIPPING_EMSPOST_RUSSIA_VALUE'    => $this->getLocationName($this->get('russia', ' - ')),
));
