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

$jbrouter = $this->app->jbrouter;

$ctrl = $this->app->jbrequest->getCtrl();
$task = $this->app->jbrequest->get('task', 'index');

$curUrl = $jbrouter->admin(['controller' => $ctrl, 'task' => $task]);
$curMenu = $ctrl;

$allItems = [
    'jbinfo'   => [
        'info' => [
            'custom'       => ['index'],
            'requirements' => ['requirements'],
        ],
        'test' => [
            'systemreport' => ['systemreport'],
            'performance'  => ['performance'],
        ]
    ],
    'jbtools'  => [
        'jbzoo'   => [
            'reindex'    => ['reindex'],
            'checkfiles' => ['checkfiles']
        ],
        'zoo'     => [
            'cleandb'    => ['cleandb'],
            'checkfiles' => ['checkfileszoo'],
        ],
        'migrate' => [
            'migrate' => ['migrate']
        ]
    ],
    'jbimport' => [
        'jbzoo' => [
            'items'      => ['items'],
            'categories' => ['categories'],
        ],
        'zoo'   => [
            'standard' => ['standard'],
        ],
    ],
    'jbconfig' => [
        'jbzoo' => [
            'index'     => ['index'],
            'zoohack'   => ['zoohack'],
            'assets'    => ['assets'],
            'yandexYml' => ['yandexYml'],
            'sef'       => ['sef'],
        ]
    ],
    'jbcart'   => [
        'basic'    => [
            'index' => ['index', 'params' => ['icon' => 'line-chart']],
        ],
        'config'   => [
            'config'   => ['config', 'params' => ['icon' => 'cog']],
            'urls'     => ['urls', 'params' => ['icon' => 'link']],
            'status'   => ['status', 'params' => ['icon' => 'bookmark']],
            'currency' => ['currency', 'params' => ['icon' => 'eur']],
        ],
        'price'    => [
            'price'             => ['price', 'params' => ['icon' => 'money']],
            'jbpriceTmpl'       => ['priceTmpl', 'params' => ['icon' => 'columns']],
            'jbpriceFilterTmpl' => ['priceFilterTmpl', 'params' => ['icon' => 'filter']],
        ],
        'order'    => [
            'fields'   => ['fields', 'params' => ['icon' => 'check-square-o']],
            'cartTmpl' => ['cartTmpl', 'params' => ['icon' => 'columns']],
        ],
        'shipping' => [
            'shipping'      => ['shipping', 'params' => ['icon' => 'truck']],
            'shippingfield' => ['shippingfield', 'params' => ['icon' => 'check-square-o']],
        ],
        'payment'  => [
            'payment' => ['payment', 'params' => ['icon' => 'credit-card']],
        ],
        'events'   => [
            'modifier_item_price'  => ['modifierItemPrice', 'params' => ['icon' => 'cubes']],
            'modifier_order_price' => ['modifierOrderPrice', 'params' => ['icon' => 'cubes']],
            'validator'            => ['validator', 'params' => ['icon' => 'cube']],
            'notification'         => ['notification', 'params' => ['icon' => 'envelope-o']],
            'statusEvents'         => ['statusEvents', 'params' => ['icon' => 'code-fork']],
        ],
        'others'   => [
            'emailTmpl' => ['emailTmpl', 'params' => ['icon' => 'envelope-o']],
        ]
    ],
    'jbexport' => [
        'jbzoo' => [
            'items'      => ['items'],
            'categories' => ['categories'],
            'types'      => ['types'],
            'yandexYml'  => ['yandexYml'],
        ],
        'zoo'   => [
            'standard'  => ['standard'],
            'zoobackup' => ['zoobackup'],
        ],
    ],
];

if (isset($allItems[$curMenu])) {
    $headItems = $allItems[$curMenu];
} else {
    return;
}

?>
<div class="sidebar-nav">
    <ul class="uk-nav uk-nav-side">

        <?php
        $html = [];
        foreach ($headItems as $headItem => $items) {

            $html[] = '<li class="uk-nav-divider"></li>';
            $html[] = '<li class="uk-nav-header">' . JText::_('JBZOO_NAV_' . $curMenu . '_' . $headItem) . '</li>';

            foreach ($items as $itemName => $urlParams) {

                $params = [];
                if (isset($urlParams['params'])) {
                    $params = $urlParams['params'];
                    unset($urlParams['params']);
                }

                $url = $jbrouter->admin(['controller' => $curMenu, 'task' => $urlParams[0]]);
                $labelKey = strtoupper('JBZOO_NAV_' . $curMenu . '_' . $headItem . '_' . $itemName);
                $name = JText::_($labelKey);

                $classes = [$headItem, $itemName];
                if (strtolower($curUrl) == strtolower($url)) {
                    $classes[] = 'uk-active';
                }

                $html[] = '<li class="' . implode(' ', $classes) . '">';
                $html[] = '<a href="' . $url . '">';
                if (isset($params['icon'])) {
                    $html[] = '<span class="menu-icon"><i class="uk-icon-' . $params['icon'] . '"></i></span> ';
                }
                $html[] = $name . '</a>';
                $html[] = '</li>';
            }

        }

        echo implode(PHP_EOL, $html);
        ?>

    </ul>
</div>
