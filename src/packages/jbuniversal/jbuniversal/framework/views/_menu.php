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

