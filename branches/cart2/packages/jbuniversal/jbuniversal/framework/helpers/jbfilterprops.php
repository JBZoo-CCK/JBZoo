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


/**
 * Class JBFilterPropsHelper
 */
class JBFilterPropsHelper extends AppHelper
{
    /**
     * Element render
     * @param       $identifier
     * @param bool $value
     * @param array $params
     * @return bool
     */
    public function elementRender($identifier, $value = null, $params = array())
    {
        //get configs
        $showCount   = (int)$params['moduleParams']->get('count', 1);
        $isDepend    = (int)$params['moduleParams']->get('depend', 1);
        $isDependCat = (int)$params['moduleParams']->get('depend_category', 0);

        $elements = $isDepend ? $this->app->jbrequest->getElements() : array();

        if ($isDependCat) {
            $categoryId = $this->app->jbrequest->getSystem('category');
            if ($categoryId > 0 && !isset($elements['_itemcategory'])) {
                $elements['_itemcategory'] = $categoryId;
            }
        }

        $propsValues = JBModelValues::model()->getPropsValues(
            $identifier,
            $params['moduleParams']->get('type'),
            $params['moduleParams']->get('application'),
            $elements
        );

        $jbrouter = $this->app->jbrouter;

        if (!empty($propsValues)) {

            $html = array();
            foreach ($propsValues as $propsValue) {

                $class = '';
                if ($this->_isActive($identifier, $propsValue['value'])) {
                    $link  = $jbrouter->filter($identifier, $propsValue['value'], $params['moduleParams'], 2);
                    $class = ' class="active"';
                } else {
                    $link = $jbrouter->filter($identifier, $propsValue['value'], $params['moduleParams'], ($isDepend ? 1 : 0));
                }

                // render html list item
                $html[] = '<li' . $class . '><a href="' . $link . '" title="' . $this->_escape($propsValue['value']) . '" rel="nofollow"><span>'
                    . $this->_escape($propsValue['value']) . ' '
                    . (($showCount) ? '<span class="element-count">(' . $propsValue['count'] . ')</span>' : '')
                    . '</span></a>'
                    . ($class ? '<a rel="nofollow" href="' . $link . '" class="cancel">&nbsp;</a>' : '')
                    . '</li>';
            }

            return '<!--noindex--><ul class="jbzoo-props-list">' . implode(PHP_EOL, $html) . '</ul><!--/noindex-->';
        }

        return '';
    }

    /**
     * Check is active
     * @param string $identifier
     * @param string $value
     * @return bool
     */
    protected function _isActive($identifier, $value)
    {
        $elements = $this->app->jbrequest->getElements();

        if (isset($elements[$identifier])) {

            if (is_string($elements[$identifier])) {
                return JString::strtolower($elements[$identifier]) == JString::strtolower(JString::trim($value));
            } else {
                return in_array($value, $elements[$identifier]);
            }
        }

        return false;
    }

    /**
     * Encode html special chars
     * @param $text
     * @return string
     */
    protected function _escape($text)
    {
        return htmlspecialchars($text, ENT_QUOTES);
    }

}
