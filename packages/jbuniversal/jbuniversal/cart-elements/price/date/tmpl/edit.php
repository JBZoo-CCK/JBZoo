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

$string = $this->app->jbstring;
$unique = $string->getId('calendar-');

if ($value = $this->getValue()) {
    try {
        $value = $this->app->html->_('date',
            $value,
            $this->app->date->format(JBCartElementPriceDate::EDIT_DATE_FORMAT),
            $this->app->date->getOffset()
        );
    } catch (Exception $e) {
    }
}

echo $this->app->html->_('zoo.calendar', $value, $this->getControlName('value'), $unique, array(
    'class' => $string->getId('calendar-element-')
), true);



