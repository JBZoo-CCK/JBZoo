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
            'index' => array('index'),
        ),
        'config' => array(
            'config'      => array('config'),
            'currency'    => array('currency'),
            'status'      => array('status'),
            'order'       => array('order'),
            'priceparams' => array('priceparams'),
            'payment'     => array('payment'),
            'delivery'    => array('delivery'),
        ),
        'events' => array(
            'modifier'     => array('modifierEvents'),
            'validator'    => array('validatorEvents'),
            'notification' => array('notificationEvents'),
            'statusEvents' => array('statusEvents'),
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

                $url = $jbrouter->admin(array('controller' => $curMenu, 'task' => $urlParams[0]));

                if (strtolower($curUrl) == strtolower($url)) {
                    $html[] = '<li class="uk-active">';
                } else {
                    $html[] = '<li>';
                }

                $labelKey = strtoupper('JBZOO_NAV_' . $curMenu . '_' . $headItem . '_' . $itemName);
                $name     = JText::_($labelKey);

                $html[] = '<a href="' . $url . '">' . $name . '</a>';
                $html[] = '</li>';
            }

        }

        echo implode("\n ", $html);
        ?>

    </ul>
</div>
