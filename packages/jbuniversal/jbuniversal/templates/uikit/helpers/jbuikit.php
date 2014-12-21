<?php
/**
 * JBZoo App is universal Joomla CCK, application for YooTheme Zoo component
 *
 * @package     jbzoo
 * @version     2.x Pro
 * @author      JBZoo App http://jbzoo.com
 * @copyright   Copyright (C) JBZoo.com,  All rights reserved.
 * @license     http://jbzoo.com/license-pro.php JBZoo Licence
 * @coder       Sergey Kalistratov <kalistratov.s.m@gmail.com>
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

/**
 * Class JBUikitHelper
 */
class JBUikitHelper extends AppHelper
{

    /**
     * Uikit pagination
     * @param $pagination
     * @param $url
     * @return string
     */
    public function paginate($pagination, $url)
    {
        $html = '';

        if ($pagination->pages() > 1) {

            $rangeStart = max($pagination->current() - $pagination->range(), 1);
            $rangeEnd   = min($pagination->current() + $pagination->range() - 1, $pagination->pages());

            if ($pagination->current() > 1) {
                $link = $url;
                $html .= '<li><a href="' . JRoute::_($link) . '">' . JText::_('JBZOO_UIKIT_PAGINATE_FIRST') . '</a></li>';
                $link = $pagination->current() - 1 == 1 ? $url : $pagination->link($url, $pagination->name() . '=' . ($pagination->current() - 1));
                $html .= '<li><a href="' . JRoute::_($link) . '"><i class="uk-icon-angle-double-left"></i></a></li>';
            }

            for ($i = $rangeStart; $i <= $rangeEnd; $i++) {
                if ($i == $pagination->current()) {
                    $html .= '<li class="uk-active"><span>' . $i . '</span>';
                } else {
                    $link = $i == 1 ? $url : $pagination->link($url, $pagination->name() . '=' . $i);
                    $html .= '<li><a href="' . JRoute::_($link) . '">' . $i . '</a></li>';
                }
            }

            if ($pagination->current() < $pagination->pages()) {
                $link = $pagination->link($url, $pagination->name() . '=' . ($pagination->current() + 1));
                $html .= '<li><a href="' . JRoute::_($link) . '"><i class="uk-icon-angle-double-right"></i></a></li>';
                $link = $pagination->link($url, $pagination->name() . '=' . ($pagination->pages()));
                $html .= '<li><a href="' . JRoute::_($link) . '">' . JText::_('JBZOO_UIKIT_PAGINATE_LAST') . '</a></li>';
            }

        }

        return $html;
    }

    /**
     * Quantity widget
     * @param int $default
     * @param array $options
     * @param string $id
     * @param string $name
     * @return string
     */
    public function quantity($default = 1, $options = array(), $id = null, $name = 'quantity')
    {
        if (!$id) {
            $id = $this->app->jbstring->getId('quantity');
        }

        $options['default'] = (float)$default;

        $html = array(
            '<table cellpadding="0" cellspacing="0" border="0" class="quantity-wrapper jsQuantity" id="' . $id . '">',
            '  <tr>',
            '    <td rowspan="2">',
            '      <div class="jsCountBox item-count-wrapper">',
            '        <div class="item-count">',
            '          <dl class="item-count-digits">' . str_repeat('<dd></dd>', 5) . '</dl>',
            '          <input type="text" class="input-quantity jsInput" maxlength="6" name="' . $name . '" value="' . $options['default'] . '">',
            '        </div>',
            '      </div>',
            '    </td>',
            '    <td class="plus"><i class="jsAdd uk-icon-plus-square-o"></i></td>',
            '  </tr>',
            '  <tr>',
            '    <td class="minus"><i class="jsRemove uk-icon-minus-square-o"></i></td>',
            '  </tr>',
            '</table>',
        );

        $this->app->jbassets->initQuantity($id, $options);

        return implode("\n", $html);
    }

}
