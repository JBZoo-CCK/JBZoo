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


$uiqueId = $this->app->jbstring->getId('courier-');

echo '<div id="' . $uiqueId . '">';
echo '<div class="courier-calendar">' . $this->_renderCalendar($params) . '</div>';
echo '<div class="courier-daytime">' . $this->_renderWeekdays($params) . ' ' . $this->_renderHours($params) . '</div>';
echo '</div>';

echo $this->app->jbassets->widget('#' . $uiqueId, 'JBZoo.ShippingType.Courier');