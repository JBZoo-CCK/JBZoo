<?php
/**
 * JBZoo App is universal Joomla CCK, application for YooTheme Zoo component
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
 * Class JBBootstrapHelper
 */
class JBBootstrapHelper extends AppHelper
{

    /**
     * Grid columns.
     *
     * @var int
     */
    protected $_grid = 12;

    /**
     * Get column number.
     *
     * @param int $cols
     * @return float
     */
    public function getColsNum($cols = 2)
    {
        $cols   = (int)$cols;
        $output = $this->_grid / $cols;

        if ($cols == 5) {
            $output = '1-' . $cols;
        }

        return $output;
    }

    /**
     * Bootstrap pagination.
     *
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

}
