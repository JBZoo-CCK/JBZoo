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

// get menu
$jbmenu = $this->app->jbmenu;

// only if JBZoo inited
if (defined('JBZOO_DISPATCHED')) {

    // create jbzoo order list tab
    if ((int)JBModelConfig::model()->get('enable', 1, 'cart.config')) {
        $tab = $jbmenu->addTab('jborders', 'jborder', 'jbzoo-orders', JText::_('JBZOO_ADMIN_ORDER_TAB'));
        $jbmenu->addItem($tab, array('controller' => 'jborder'));
    }

    // create jbzoo tools tab
    $tab = $jbmenu->addTab('jbtools', 'jbindex', 'jbzoo');
    $jbmenu->addItem($tab, array('controller' => 'jbindex'));
    $jbmenu->addItem($tab, array('controller' => 'jbcart'));
    $jbmenu->addItem($tab, array('controller' => 'jbimport'));
    $jbmenu->addItem($tab, array('controller' => 'jbexport'));
    $jbmenu->addItem($tab, array('controller' => 'jbtools'));
    $jbmenu->addItem($tab, array('controller' => 'jbconfig'));
    $jbmenu->addItem($tab, array('controller' => 'jbinfo'));
}

// render menu
echo $jbmenu->renderAdmin();

