<?php
/**
 * JBZoo App is universal Joomla CCK, application for YooTheme Zoo component
 * @package     jbzoo
 * @version     2.x Pro
 * @author      JBZoo App http://jbzoo.com
 * @copyright   Copyright (C) JBZoo.com,  All rights reserved.
 * @license     http://jbzoo.com/license-pro.php JBZoo Licence
 * @coder       Alexander Oganov <t_tapak@yahoo.com>
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

/** @var array $fields */
/** @var JBCartOrder $order */
$fields = $this->_getFields();
$order  = $this->getOrder();

$output = array();

foreach ($fields as $identifier => $elementData) {

    /** @var JBCartElementOrderEmail $element */
    if ($element = $order->getFieldElement($identifier)) {
        $element->bindData((array)$elementData);

        $params   = $this->app->data->create();
        $output[] = "<dt>{$element->getName()}</dt><dd>{$element->edit($params)}</dd>";
    }
}

if (count($output)) {
    echo '<dl class="uk-description-list-horizontal">' . implode(PHP_EOL, $output) . '</dl>';
}
