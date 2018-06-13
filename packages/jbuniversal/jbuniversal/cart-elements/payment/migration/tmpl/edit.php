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

$data = (array)$this->data();
//unset($data['rate']);
//echo '<pre>', print_r($data, true), '</pre>';

echo $this->app->jbhtml->dataList(array(
    'JBZOO_ELEMENT_PAYMENT_MIGRATION_PAYMENT_DATE'      => $this->get('payment_date'),
    'JBZOO_ELEMENT_PAYMENT_MIGRATION_PAYMENT_SYSTEM'    => $this->get('payment_system'),
    'JBZOO_ELEMENT_PAYMENT_MIGRATION_ADDITIONAL_STATUS' => $this->get('additional_status'),
));
