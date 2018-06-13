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

$key        = $this->_item->id . $this->identifier;
$attributes = array(
    'id'    => $this->app->jbstring->getId('jbprice-'),
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
        str_replace('jbprice', 'jbprice-type-', $this->getElementType())
    )
);

$html = array(
    '<div ' . $this->app->jbhtml->buildAttrs($attributes) . '>',
    $data,
    $this->app->jbassets->widget('.jsPrice-' . $this->_item->id . '-' . $this->identifier, 'JBZoo.Price', array(
        'hash'       => $hash,
        'itemId'     => $this->_item->id,
        'identifier' => $this->identifier,
        'variantUrl' => $variantUrl,
    ), true, true),
    '</div>'
);

echo
$this->app->jbassets->mergeVar($key . '.elements', $elements),
$this->app->jbassets->mergeVar($key . '.template', array($hash => $this->getTemplate())),
implode(PHP_EOL, $html);