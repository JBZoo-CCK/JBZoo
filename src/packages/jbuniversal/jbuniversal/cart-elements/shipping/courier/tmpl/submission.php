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

$uiqueId = $this->app->jbstring->getId('courier-');

echo '<div id="' . $uiqueId . '">';
echo '<div class="courier-calendar">' . $this->_renderCalendar($params) . '</div>';
echo '<div class="courier-daytime">' . $this->_renderWeekdays($params) . ' ' . $this->_renderHours($params) . '</div>';
echo '</div>';

echo $this->app->jbassets->widget('#' . $uiqueId, 'JBZoo.ShippingType.Courier');