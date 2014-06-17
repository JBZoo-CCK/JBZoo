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


// load config
require_once dirname(__FILE__) . '/helper.php';

$propsHelper = new JBZooFilterPropsHelper($params);
$zoo         = App::getInstance('zoo');

$zoo->jbdebug->mark('mod_jbzoo_props::start-' . $module->id);

$zoo->jbassets->setAppCSS();
$zoo->jbassets->setAppJS();

// get params
$type        = $params->get('type', 0);
$application = $params->get('application', 0);

// compatibility params with v2.x
if ($params->get('item_layout')) {
    $itemLayout   = $params->get('item_layout', 'default');
    $moduleLayout = $params->get('layout', 'default');

} else {
    $itemLayout   = $params->get('layout', 'default');
    $moduleLayout = $params->get('module-layout', 'default');
}

if ($type && $application && $itemLayout) {

    // prepeare
    $zoo->jbfilter->set($type, $application);

    // get application instance
    $application = $zoo->table->application->get($application);

    // set renderer
    $renderer = $zoo->renderer->create('filterProps')
        ->addPath(array(
            $zoo->path->path('component.site:'),
            dirname(__FILE__)
        ))
        ->setModuleParams($params);

    // render
    include(JModuleHelper::getLayoutPath('mod_jbzoo_props', $moduleLayout));
}


$zoo->jbdebug->mark('mod_jbzoo_props::finish-' . $module->id);