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


// load config
require_once dirname(__FILE__) . '/helper.php';

$filterHelper = new JBZooFilterHelper($params, $module);

$zoo = App::getInstance('zoo');

$zoo->jbdebug->mark('mod_jbzoo_search::start-' . $module->id);

// get params
$type         = $params->get('type');
$application  = $params->get('application', 0);
$itemLayout   = $filterHelper->getItemLayout();
$moduleLayout = $filterHelper->getModuleLayout();

if ($type && $application && $itemLayout) {

    // load important assets
    $zoo->jbassets->setAppCSS();
    $zoo->jbassets->setAppJS();
    $zoo->jbassets->tools();
    $zoo->jbassets->less('mod_jbzoo_search:assets/less/filter.less');
    $zoo->jbassets->js('mod_jbzoo_search:assets/js/filter.js');

    // init filter widget
    $zoo->jbassets->widget('#' . $filterHelper->getFormId(), 'JBZoo.Filter', array(
        'autosubmit' => (int)$params->get('autosubmit', 0)
    ));

    // prepeare helper
    $zoo->jbfilter->set($type, $application); // TODO kill me

    // render
    echo $filterHelper->partial($moduleLayout);
}

$zoo->jbdebug->mark('mod_jbzoo_search::finish-' . $module->id);
