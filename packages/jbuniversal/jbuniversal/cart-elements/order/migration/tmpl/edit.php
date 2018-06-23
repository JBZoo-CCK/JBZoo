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

$fields = $this->get('content', array());
if ($fields) {

    $html[] = '<div class="uk-overflow-container"><table class="uk-table uk-table-condensed"><tbody>';
    $html[] = '<thead><tr><th>Property</th><th>Value</th><tr></thead>';

    foreach ($fields as $key => $value) {
        $html[] = '<tr>';
        $html[] = '<td class="uk-width-1-4">' . $key . '</td>';
        $html[] = '<td>' . $value . '</td>';
        $html[] = '</tr>';
    }

    $html[] = '</tbody></table></div>';

} else {
    $html[] = '-';
}

echo implode(PHP_EOL, $html);
?>


