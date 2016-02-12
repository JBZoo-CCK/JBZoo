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

$key = $this->_item->id . $this->identifier;
$id  = $this->app->jbstring->getId('jbprice-');

$attributes = array(
    'id'    => $id,
    'class' => array(
        // for JS
        'jsPrice-' . $this->_item->id . '-' . $this->identifier,
        'jsPrice',                                                  // is correct class for JS
        'jsJBPrice',                                                // TODO kill me (but JS bugs!)
        'jsJBPrice-' . $this->identifier . '-' . $this->_item->id,  // to group elements
        $hash,                                                      // TODO add prefix "jsPrice-" (but JS bugs!)

        // for design
        'jbprice',
        'jbprice-tmpl-' . $this->_template,
        str_replace('jbprice', 'jbprice-type-', $this->getElementType()),
    ),
);

$html = array(
    '<div ' . $this->app->jbhtml->buildAttrs($attributes) . '>',
    $data,
    $this->app->jbassets->widget('#' . $id, 'JBZoo.Price', array(
        'hash'       => $hash,
        'itemId'     => $this->_item->id,
        'identifier' => $this->identifier,
        'variantUrl' => $variantUrl,
    ), true, true),
    '</div>',
);

echo $this->app->jbassets->mergeVar($key . '.elements', $elements);
echo $this->app->jbassets->mergeVar($key . '.template', array($hash => $this->getTemplate()));
echo implode(PHP_EOL, $html);
