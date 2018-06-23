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


