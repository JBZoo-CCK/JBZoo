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
