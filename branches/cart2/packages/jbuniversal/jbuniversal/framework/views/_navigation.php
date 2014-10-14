<?php
/**
 * JBZoo App is universal Joomla CCK, application for YooTheme Zoo component
 *
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

$curUrl = $jbrouter->admin(array('controller' => $ctrl, 'task' => $task));
$curMenu = $ctrl;

$allItems = array(
    'jbinfo'   => array(
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
    'jbtools'  => array(
        'jbzoo' => array(
            'reindex'    => array('reindex'),
            'checkfiles' => array('checkfiles'),
        ),
        'zoo'   => array(
            'cleandb'    => array('cleandb'),
            'checkfiles' => array('checkfileszoo'),
        )
    ),
    'jbimport' => array(
        'jbzoo' => array(
            'items'      => array('items'),
            'categories' => array('categories'),
        ),
        'zoo'   => array(
            'standart' => array('standart'),
        ),
    ),
    'jbconfig' => array(
        'jbzoo' => array(
            'index'     => array('index'),
            'yandexYml' => array('yandexYml'),
            'sef'       => array('sef'),
        )
    ),
    'jbcart'   => array(
        'basic'  => array(
            'index' => array('index', 'params' => array('icon' => 'line-chart')),
        ),
        'config' => array(
            'config'        => array('config', 'params' => array('icon' => 'cog')),
            'currency'      => array('currency', 'params' => array('icon' => 'eur')),
            'status'        => array('status', 'params' => array('icon' => 'bookmark')),
            'fields'        => array('fields', 'params' => array('icon' => 'check-square-o')),
            'price'         => array('price', 'params' => array('icon' => 'money')),
            'payment'       => array('payment', 'params' => array('icon' => 'credit-card')),
            'shipping'      => array('shipping', 'params' => array('icon' => 'truck')),
            'shippingfield' => array('shippingfield', 'params' => array('icon' => 'check-square-o')),
        ),
        'events' => array(
            'modifier'     => array('modifier', 'params' => array('icon' => 'cubes')),
            'validator'    => array('validator', 'params' => array('icon' => 'cube')),
            'notification' => array('notification', 'params' => array('icon' => 'envelope-o')),
            'statusEvents' => array('statusEvents', 'params' => array('icon' => 'code-fork')),
        ),
        'tmpl'   => array(
            'cartTmpl'          => array('cartTmpl', 'params' => array('icon' => 'columns')),
            'jbpriceTmpl'       => array('jbpriceTmpl', 'params' => array('icon' => 'money')),
            'jbpriceFilterTmpl' => array('jbpriceFilterTmpl', 'params' => array('icon' => 'filter')),
            'emailTmpl'         => array('emailTmpl', 'params' => array('icon' => 'envelope-o')),
        )
    ),
    'jbexport' => array(
        'jbzoo' => array(
            'items'      => array('items'),
            'categories' => array('categories'),
            'types'      => array('types'),
            'yandexYml'  => array('yandexYml'),
        ),
        'zoo'   => array(
            'standart'  => array('standart'),
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

        echo implode("\n ", $html);
        ?>

    </ul>
</div>
