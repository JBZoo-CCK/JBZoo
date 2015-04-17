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


$attrs = array(
    'id'    => $this->app->jbstring->getId('jbprice-'),
    'class' => array(
        // for JS
        'jsPrice',                                                  // is correct class for JS
        'jsJBPrice',                                                // TODO kill me (but JS bugs!)
        'jsJBPrice-' . $this->identifier . '-' . $this->_item->id,  // to group elements
        $hash,                                                      // TODO add prefix "jsPrice-" (but JS bugs!)

        // for design
        'jbprice',
        'jbprice-tmpl-' . $this->_template,
        str_replace('jbprice', 'jbprice-type-', $this->getElementType()),    // TODO replace to "jbprice-type-<plain|calc>"
    )
);

$html = array(
    '<div ' . $this->app->jbhtml->buildAttrs($attrs) . '>',
    $data,
    $this->app->jbassets->widget('#' . $attrs['id'], 'JBZoo.Price', array(
        'hash'       => $hash,
        'itemId'     => $this->_item->id,
        'identifier' => $this->identifier,
        'variantUrl' => $variantUrl,
        'elements'   => $elements,
    ), true),
    '</div>'
);

echo implode(PHP_EOL, $html);
