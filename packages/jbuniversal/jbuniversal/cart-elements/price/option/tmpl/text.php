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

$html = array();
foreach ($data as $optionName => $optionVal) {
    if ($optionName) {
        $className  = $this->app->string->sluggify($optionName);
        $optionName = JString::ucfirst($optionName);
        $html[]     = '<span class="jbprice-option-' . $className . '">' . $optionName . '</span>';
    }
}

echo '<span class="jbprice-option-text">' . implode(', ', $html) . '</span>';
