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


// get render
$view    = $this->getView();
$render  = $view->renderer;
$tooltip = '';

// load assets
$this->app->jbassets->initTooltip();
$this->app->jbassets->compare();

// render table cells items
$renderedItems = $render->renderFields($view->itemType, $view->appId, $vars['objects']);
$elementList   = $render->getElementList($renderedItems);

// render top compare links
$horizontalUrl = $this->app->jbrouter->compare($view->itemId, 'h', $view->itemType, $view->appId);
$verticalUrl   = $this->app->jbrouter->compare($view->itemId, 'v', $view->itemType, $view->appId);
$clearUrl      = $this->app->jbrouter->compareClear($view->itemId, $view->itemType, $view->appId);

$html = array();

// add links
$html[] = '<div class="jbcompare-links">';
if ($view->layoutType == 'h') {
    $html[] = '<a href="' . $verticalUrl . '" class="jbbutton small">' . JTEXT::_('JBZOO_COMPARE_VERTICAL') . '</a>';
} else {
    $html[] = '<a href="' . $horizontalUrl . '" class="jbbutton small">' . JTEXT::_('JBZOO_COMPARE_HORIZONTAL') . '</a>';
}
$html[] = '<a href="' . $clearUrl . '" class="jbbutton small orange jbcompare-clear">' . JText::_('JBZOO_COMPARE_REMOVEALL') . '</a>';
$html[] = '</div>';

// render compare table html
if ($view->layoutType == 'v') {

    // header
    $html[] = '<table class="jbcompare-table vertical jsCompareTable">';
    $html[] = '<thead><tr><td class="jbcompare-names">&nbsp;</td>';

    foreach ($renderedItems as $itemId => $itemHtml) {
        $link   = $this->app->route->item($vars['objects'][$itemId]);
        $title  = $itemHtml['itemname'];
        $html[] = '<th><a href="' . $link . '" title="' . $title . '">' . $title . '</a></th>';
    }
    $html[] = '</tr></thead><tbody>';

    foreach ($elementList as $elementId) {

        if ($elementId != 'itemname') {
            $label       = $render->renderElementLabel($elementId, $view->itemType, $view->appId);
            $element     = $this->app->jbentity->getElement($elementId, $view->itemType, $view->appId);
            $tooltipText = $this->app->jbstring->clean($element->config->get('description'));
            $tooltip     = $tooltipText ? ' <span class="jbtooltip" title="' . $tooltipText . '"></span>' : '';

            $html[] = '<tr class="jbcompare-row"><th>' . $label . $tooltip . '</th>';
            foreach ($renderedItems as $itemId => $itemElements) {
                $html[] = '<td class="jbcompare-cell">' . $itemElements[$elementId] . '</td>';
            }
            $html[] = '</tr>';
        }

    }

    $html[] = '</tbody></table>';

} else if ($view->layoutType == 'h') {

    // header
    $html[] = '<table class="jbcompare-table horizontal jsCompareTable">';
    $html[] = '<thead><tr><td class="jbcompare-item-names">&nbsp;</td>';
    foreach ($elementList as $elementId) {
        $element = $this->app->jbentity->getElement($elementId, $view->itemType, $view->appId);
        if ($element) {
            $tooltipText = $this->app->jbstring->clean($element->config->get('description'));
            $tooltip     = $tooltipText ? ' <span class="jbtooltip" title="' . $tooltipText . '"></span>' : '';
        }

        if ($elementId != 'itemname') {
            $html[] = '<th>' . $render->renderElementLabel($elementId, $view->itemType, $view->appId) . $tooltip . '</th>';
        }
    }
    $html[] = '</tr></thead><tbody>';

    // body
    foreach ($renderedItems as $itemId => $itemElements) {

        $html[] = '<tr class="jbcompare-row">';
        foreach ($itemElements as $elementId => $elementHtml) {

            if ($elementId == 'itemname') {
                $link   = $this->app->route->item($vars['objects'][$itemId]);
                $html[] = '<th><a href="' . $link . '">' . $elementHtml . '</a></th>';
            } else {
                $html[] = '<td class="jbcompare-cell jbcompare-cell-' . $elementId . '"'
                    . ' data-elementid="' . $elementId . '">' . $elementHtml . '</td>';
            }
        }
        $html[] = '</tr>';
    }

    $html[] = '</tbody></table>';
}


echo implode("\n", $html);

?>

<?php echo $this->app->jbassets->widget('.jsCompareTable', 'JBZooCompareTable', array(
    'dir' => $view->layoutType,
), true); ?>
