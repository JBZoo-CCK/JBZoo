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


