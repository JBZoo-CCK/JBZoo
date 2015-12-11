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

$jbrouter = $this->app->jbrouter;

$ctrl = $this->app->jbrequest->getCtrl();
$task = $this->app->jbrequest->get('task', 'index');

$curUrl  = $jbrouter->admin(array('controller' => $ctrl, 'task' => $task));
$curMenu = $ctrl;

$allItems = array(
    'jbinfo'    => array(
        'info' => array(
            'custom'       => array('index'),
            'requirements' => array('requirements'),
            'licence'      => array('licence'),
        ),
        'test' => array(
            'systemreport' => array('systemreport'),
            'performance'  => array('performance'),
        )
    ),
    'jbtools'   => array(
        'jbzoo'   => array(
            'reindex'    => array('reindex'),
            'checkfiles' => array('checkfiles')
        ),
        'zoo'     => array(
            'cleandb'    => array('cleandb'),
            'checkfiles' => array('checkfileszoo'),
        ),
        'migrate' => array(
            'migrate' => array('migrate')
        )
    ),
    'jbimport'  => array(
        'jbzoo' => array(
            'items'      => array('items'),
            'categories' => array('categories'),
        ),
        'zoo'   => array(
            'standard' => array('standard'),
        ),
    ),
    'jbconfig'  => array(
        'jbzoo' => array(
            'index'     => array('index'),
            'assets'    => array('assets'),
            'yandexYml' => array('yandexYml'),
            'sef'       => array('sef'),
        )
    ),
    'jbcart'    => array(
        'basic'    => array(
            'index' => array('index', 'params' => array('icon' => 'line-chart')),
        ),
        'config'   => array(
            'config'   => array('config', 'params' => array('icon' => 'cog')),
            'urls'     => array('urls', 'params' => array('icon' => 'link')),
            'status'   => array('status', 'params' => array('icon' => 'bookmark')),
            'currency' => array('currency', 'params' => array('icon' => 'eur')),
        ),
        'price'    => array(
            'price'             => array('price', 'params' => array('icon' => 'money')),
            'jbpriceTmpl'       => array('priceTmpl', 'params' => array('icon' => 'columns')),
            'jbpriceFilterTmpl' => array('priceFilterTmpl', 'params' => array('icon' => 'filter')),
        ),
        'order'    => array(
            'fields'   => array('fields', 'params' => array('icon' => 'check-square-o')),
            'cartTmpl' => array('cartTmpl', 'params' => array('icon' => 'columns')),
        ),
        'shipping' => array(
            'shipping'      => array('shipping', 'params' => array('icon' => 'truck')),
            'shippingfield' => array('shippingfield', 'params' => array('icon' => 'check-square-o')),
        ),
        'payment'  => array(
            'payment' => array('payment', 'params' => array('icon' => 'credit-card')),
        ),
        'events'   => array(
            'modifier_item_price'  => array('modifierItemPrice', 'params' => array('icon' => 'cubes')),
            'modifier_order_price' => array('modifierOrderPrice', 'params' => array('icon' => 'cubes')),
            'validator'            => array('validator', 'params' => array('icon' => 'cube')),
            'notification'         => array('notification', 'params' => array('icon' => 'envelope-o')),
            'statusEvents'         => array('statusEvents', 'params' => array('icon' => 'code-fork')),
        ),
        'others'   => array(
            'emailTmpl' => array('emailTmpl', 'params' => array('icon' => 'envelope-o')),
        )
    ),
    'jbexport'  => array(
        'jbzoo' => array(
            'items'      => array('items'),
            'categories' => array('categories'),
            'types'      => array('types'),
            'yandexYml'  => array('yandexYml'),
        ),
        'zoo'   => array(
            'standard'  => array('standard'),
            'zoobackup' => array('zoobackup'),
        ),
    ),
);

if (isset($allItems[$curMenu])) {
    $headItems = $allItems[$curMenu];
} else {
    return;
}

?>
<div class="sidebar-nav">
    <ul class="uk-nav uk-nav-side">

        <?php
        $html = array();
        foreach ($headItems as $headItem => $items) {

            $html[] = '<li class="uk-nav-divider"></li>';
            $html[] = '<li class="uk-nav-header">' . JText::_('JBZOO_NAV_' . $curMenu . '_' . $headItem) . '</li>';

            foreach ($items as $itemName => $urlParams) {

                $params = array();
                if (isset($urlParams['params'])) {
                    $params = $urlParams['params'];
                    unset($urlParams['params']);
                }

                $url      = $jbrouter->admin(array('controller' => $curMenu, 'task' => $urlParams[0]));
                $labelKey = strtoupper('JBZOO_NAV_' . $curMenu . '_' . $headItem . '_' . $itemName);
                $name     = JText::_($labelKey);

                $classes = array($headItem, $itemName);
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
