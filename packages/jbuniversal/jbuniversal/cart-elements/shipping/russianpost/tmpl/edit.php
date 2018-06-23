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

$typeList = $this->_getTypePostList();

echo $this->app->jbhtml->dataList(array(
    'JBZOO_ELEMENT_SHIPPING_RUSSIANPOST_TYPE' => $typeList[$this->get('typePost')],
    'JBZOO_ELEMENT_SHIPPING_RUSSIANPOST_ZIP'  => $this->get('postOfficeId'),
));
