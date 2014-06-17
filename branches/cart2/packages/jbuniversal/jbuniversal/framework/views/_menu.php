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

// get menu
$jbmenu = $this->app->jbmenu;

// only if JBZoo inited
if (defined('JBZOO_DISPATCHED')) {

    // create new tab
    $tab = $jbmenu->addTab('jbtools', 'jbindex', 'jbzoo');

    // add children items
    $jbmenu->addItem($tab, array('controller' => 'jbindex'));
    $jbmenu->addItem($tab, array('controller' => 'jbimport'));
    $jbmenu->addItem($tab, array('controller' => 'jbexport'));
    $jbmenu->addItem($tab, array('controller' => 'jbtools'));
    $jbmenu->addItem($tab, array('controller' => 'jbconfig'));
    $jbmenu->addItem($tab, array('controller' => 'jbinfo'));
}

// render menu
echo $jbmenu->renderAdmin();

