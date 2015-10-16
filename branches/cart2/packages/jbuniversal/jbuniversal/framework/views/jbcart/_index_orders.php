<?php
/**
 * JBZoo App is universal Joomla CCK, application for YooTheme Zoo component
 * @package     jbzoo
 * @version     2.x Pro
 * @author      JBZoo App http://jbzoo.com
 * @copyright   Copyright (C) JBZoo.com,  All rights reserved.
 * @license     http://jbzoo.com/license-pro.php JBZoo Licence
 * @coder       Denis Smetannikov <denis@jbzoo.com>
 */

// no direct access
defined('_JEXEC') or die('Restricted access');


if (!empty($this->orders)) {

    $html = array(
        '<br><h2>' . JText::_('JBZOO_ADMIN_INDEX_ORDERS_TITLE') . '</h2>',
        '<table class="uk-table uk-table-hover uk-table-striped uk-table-condensed"><thead><tr>',
        '<th>' . JText::_('JBZOO_ADMIN_NAME') . '</th>',
        '<th>' . JText::_('JBZOO_ADMIN_CREATED') . '</th>',
        '<th>' . JText::_('JBZOO_ADMIN_STATUS') . '</th>',
        '<th>' . JText::_('JBZOO_ADMIN_TOTAL') . '</th>',
        '<th>' . JText::_('JBZOO_ADMIN_ORDER_COMMENT') . '</th>',
        '</tr></thead>',
    );

    foreach ($this->orders as $order) {
        $html[] = '<tr>';

        $html[] = '<td>';
        $html[] = '<a href="' . $order->getUrl() . '">№' . $order->getName() . '</a> ' . JText::_('JBZOO_BY');
        if ($user = $order->getAuthor()) {
            $href   = $this->app->component->users->link(array('task' => 'user.edit', 'layout' => 'edit', 'view' => 'user', 'id' => $user->id));
            $html[] = '<i><a href="' . $href . '">' . $user->name . '</a></i>';
        } else {
            $html[] = '<i>' . JText::_('JBZOO_ANONYM') . '</i>';
        }
        $html[] = '</td>';

        $html[] = '<td>' . $this->app->jbdate->toHuman($order->created) . '</td>';
        $html[] = '<td>' . $order->getStatus()->getName() . '</td>';
        $html[] = '<td>' . $order->getTotalSum(true) . '</td>';
        $html[] = '<td>' . (($order->comment) ? $order->comment : ' - ') . '</td>';

        $html[] = '</tr>';
    }

    $html[] = '</table>';
    echo implode(PHP_EOL, $html);
}


