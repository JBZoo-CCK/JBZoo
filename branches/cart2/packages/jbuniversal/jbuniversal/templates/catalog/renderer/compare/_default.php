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


$this->app->jbdebug->mark('layout::compare::start');

// get render
$view = $this->getView();
$render = $view->renderer;
$tooltip = '';
// render table cells items
$renderedItems = $render->renderFields($view->itemType, $view->appId, $vars['objects']);
$elementList = $render->getElementList($renderedItems);

$this->app->jbdebug->mark('layout::compare::renerItems');

// render top compare links
$horizontalUrl = $this->app->jbrouter->compare($view->itemId, 'h', $view->itemType, $view->appId);
$verticalUrl = $this->app->jbrouter->compare($view->itemId, 'v', $view->itemType, $view->appId);
$clearUrl = $this->app->jbrouter->compareClear($view->itemId, $view->itemType, $view->appId);

// add links
echo '<div class="jbzoo-compare-links">';
if ($view->layoutType == 'h') {
    echo '<a href="' . $verticalUrl . '">' . JTEXT::_('JBZOO_COMPARE_VERTICAL') . '</a> &mdash; '
        . '<span>' . JTEXT::_('JBZOO_COMPARE_HORIZONTAL') . '</span>';
} else {
    echo '<span>' . JTEXT::_('JBZOO_COMPARE_VERTICAL') . '</span> &mdash; '
        . '<a href="' . $horizontalUrl . '">' . JTEXT::_('JBZOO_COMPARE_HORIZONTAL') . '</a>';
}

echo '<a href="' . $clearUrl . '" title="' . JText::_('JBZOO_COMPARE_REMOVEALL') . '" class="compare-clear">' . JText::_('JBZOO_COMPARE_REMOVEALL') . '</a>';
echo '</div>';

$this->app->jbdebug->mark('layout::compare::renerLins');

// render compare table html
if ($view->layoutType == 'v') {

    echo '<table class="jbcompare-table vertical">';

    // head
    echo '<thead><tr><td class="element-names">&nbsp;</td>';
    foreach ($renderedItems as $itemId => $itemHtml) {
        $link  = $this->app->route->item($vars['objects'][$itemId]);
        $title = $itemHtml['itemname'];
        echo '<th><a href="' . $link . '" title="' . $title . '">' . $title . '</a></th>' . "\n";
    }
    echo '</tr></thead>';

    // body
    echo '<tbody>';
    foreach ($elementList as $elementId) {

        if ($elementId != 'itemname') {

            $label = $render->renderElementLabel($elementId, $view->itemType, $view->appId);

            $element     = $this->app->jbentity->getElement($elementId, $view->itemType, $view->appId);
            $tooltipText = $this->app->jbstring->clean($element->config->get('description'));
            $tooltip     = $tooltipText ? ' <span class="jbtooltip" title="' . $tooltipText . '"></span>' : '';

            if ($tooltipText) {
                $this->app->jbassets->initTooltip();
            }

            echo '<tr class="compare-row"><th>' . $label . $tooltip . '</th>';
            foreach ($renderedItems as $itemId => $itemElements) {
                echo '<td class="compare-cell">' . $itemElements[$elementId] . '</td>' . "\n";
            }
            echo '</tr>';
        }

    }

    echo '</tbody></table>';
    ?>
    <script type="text/javascript">
        (function ($) {
            $('.jbcompare-table .compare-row').each(function (n, obj) {

                var $obj = $(obj), data = undefined, isEqual = true;
                var $cells = $('.compare-cell', $obj);

                if ($cells.length > 1) {
                    $cells.each(function (k, cell) {
                        var cellData = $.trim($(cell).text()).toLowerCase();

                        if (data === undefined) {
                            data = cellData;
                        } else {
                            isEqual = data == cellData;
                        }

                        if (!isEqual) {
                            $obj.addClass('compare-not-equal');
                        }
                    });
                }
            });
        })(jQuery);
    </script>
<?php

} else if ($view->layoutType == 'h') {

    echo '<table class="jbcompare-table horizontal">';

    echo '<thead><tr><td class="item-names">&nbsp;</td>';
    foreach ($elementList as $elementId) {
        $element = $this->app->jbentity->getElement($elementId, $view->itemType, $view->appId);
        if ($element) {
            $tooltipText = $this->app->jbstring->clean($element->config->get('description'));
            $tooltip     = $tooltipText ? ' <span class="jbtooltip" title="' . $tooltipText . '"></span>' : '';

            if ($tooltipText) {
                $this->app->jbassets->initTooltip();
            }
        }
        if ($elementId != 'itemname') {
            echo '<th>' . $render->renderElementLabel($elementId, $view->itemType, $view->appId) . $tooltip . '</th>' . "\n";
        }
    }
    echo '</tr></thead>';

    echo '<tbody>';
    echo '<meta charset="utf8" />';

    foreach ($renderedItems as $itemId => $itemElements) {

        echo '<tr class="compare-row">';
        foreach ($itemElements as $elementId => $elementHtml) {

            if ($elementId == 'itemname') {
                $link  = $this->app->route->item($vars['objects'][$itemId]);
                $title = $elementHtml;

                echo '<th><a href="' . $link . '" title="' . $title . '">' . $title . '</a></th>' . "\n";

            } else {

                echo '<td class="compare-cell ' . $elementId . '" data-elementid="' . $elementId . '">' . $elementHtml . '</td>' . "\n";
            }
        }
        echo '</tr>';
    }

    echo '</tbody></table>';
    ?>
    <script type="text/javascript">
        (function ($) {

            var $cols = $('.jbcompare-table .compare-row:first .compare-cell');

            $cols.each(function (n, mainCell) {

                var $mainCell = $(mainCell),
                    elementid = $mainCell.data('elementid'),
                    $cells = $('.jbcompare-table .compare-cell.' + elementid),
                    data = undefined,
                    isEqual = true;

                if ($cells.length > 1) {
                    $cells.each(function (k, cell) {

                        var $cell = $(cell), cellData = $.trim($cell.text()).toLowerCase();

                        if (data === undefined) {
                            data = cellData;
                        } else {
                            isEqual = data == cellData;
                        }

                        if (!isEqual) {
                            $cells.addClass('compare-not-equal');
                        }
                    });
                }

            });
        })(jQuery);
    </script>
<?php

} else {
    throw new AppException($view->layoutType . ' - Unknow layout!');
}

$this->app->jbdebug->mark('layout::compare::finish');
