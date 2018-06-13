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

echo $this->app->jbhtml->dataList(array(
    'JBZOO_ELEMENT_SHIPPING_EMSPOST_CITIES_VALUE'    => $this->getLocationName($this->get('cities', ' - ')),
    'JBZOO_ELEMENT_SHIPPING_EMSPOST_COUNTRIES_VALUE' => $this->getLocationName($this->get('countries', ' - ')),
    'JBZOO_ELEMENT_SHIPPING_EMSPOST_REGIONS_VALUE'   => $this->getLocationName($this->get('regions', ' - ')),
    'JBZOO_ELEMENT_SHIPPING_EMSPOST_RUSSIA_VALUE'    => $this->getLocationName($this->get('russia', ' - ')),
));
