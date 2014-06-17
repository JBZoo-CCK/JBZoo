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


$zoo = App::getInstance('zoo');
$zoo->jbassets->initJBPrice();

$rowCounter = (int)$params->get('counter');

echo '<label class="jbprice-row jbprice-row-' . $rowCounter . '">';
$htmlValues = array();

if ($template == 'oneclick') {

    $checked = '';
    if ($rowCounter == 0) {
        $checked = 'checked="checked"';
    }

    echo '<input type="radio" ' . $checked . ' value="' . $rowCounter . '"
        name="jbprice-' . $this->getItem()->id . '-' . $params->get('uniqid') . '" />&nbsp;';

}

foreach ($values as $currency => $value) {

    $activeClass = '';
    if ($currency == $activeCur) {
        $activeClass = ' active';
    }

    if ($value['noFormat'] > 0) {
        $htmlValues[] = '<span class="price-value jsPriceValue price-currency-' . $currency . $activeClass . '">' . $value['format'] . '</span>';
    }
}


echo implode("\n", $htmlValues) . "\n";

if ($description) {
    echo '<span class="description">' . $description . '</span> ';
}

echo '</label>';
