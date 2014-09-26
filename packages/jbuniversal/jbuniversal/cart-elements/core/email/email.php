<?php
/**
 * JBZoo App is universal Joomla CCK, application for YooTheme Zoo component
 *
 * @package     jbzoo
 * @version     2.x Pro
 * @author      JBZoo App http://jbzoo.com
 * @copyright   Copyright (C) JBZoo.com,  All rights reserved.
 * @license     http://jbzoo.com/license-pro.php JBZoo Licence
 * @coder       Alexander Oganov <t_tapak@yahoo.com>
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

/**
 * Class JBCartElementEmail
 */
abstract class JBCartElementEmail extends JBCartElement
{
    /**
     * Default cart options
     * @var JSONData
     */
    protected $_cartConfig;

    /**
     * @var JBMoneyHelper
     */
    protected $_jbmoney;

    /**
     * @var JBCartOrder|Comment
     */
    protected $_subject;

    /**
     * Class constructor
     *-+
     *     * @param App $app
     *
     * @param string $type
     * @param string $group
     */
    public function __construct($app, $type, $group)
    {
        parent::__construct($app, $type, $group);

        $this->_cartConfig = $this->_getCartConfig();
        $this->_jbmoney    = $this->app->jbmoney;
    }

    /**
     * Check elements value.
     * Output element or no.
     *
     * @param  array $params
     *
     * @return bool
     */
    public function hasValue($params = array())
    {
        return true;
    }

    /**
     * Render elements data
     *
     * @param  array $params
     *
     * @return null|string
     */
    public function render($params = array())
    {
        if ($layout = $this->getLayout($params->get('_layout') . '.php')) {
            return self::renderLayout($layout, array(
                'params' => $params,
                'order'  => $this->getOrder()
            ));
        }

        return false;
    }

    /**
     * Try to get currency from order or cart config
     *
     * @return mixed
     */
    public function currency()
    {
        $currency = $this->_cartConfig->get('default_currency', 'EUR');
        if (isset($this->_order->id)) {
            $currency = $this->_order->getCurrency();
        }

        return $currency;
    }

    /**
     * Get title from config or default
     *
     * @param  null|string $default
     *
     * @return null|string
     */
    public function getTitle($default = null)
    {
        $title = $this->config->get('title');
        $title = !empty($title) ? $title : $default;

        return JText::_($title);
    }

    /**
     * @param        $layout
     * @param  array $params
     *
     * @return null|string
     */
    public function partial($layout, $params = array())
    {
        if ($layout = $this->getLayout($layout . '.php')) {
            return self::renderLayout($layout, $params);
        }

        return null;
    }

    /**
     * @param $subject
     *
     * @return $this
     */
    public function setSubject($subject)
    {
        $this->_subject = $subject;

        if (get_class($subject) == 'JBCartOrder') {
            $this->setOrder($subject);
        }

        return $this;
    }

    /**
     * @return Comment|JBCartOrder
     */
    public function getSubject()
    {
        return $this->_subject;
    }

    /**
     * Cleans data
     *
     * @param  string         $data
     * @param  string|boolean $charlist
     *
     * @return string mixed
     */
    public function clean($data, $charlist = false)
    {
        if (!is_array($data)) {
            return $this->_clean($data, $charlist);
        }

        foreach ($data as $key => $value) {
            $data[$this->_clean($key, $charlist)] = $this->_clean($value, $charlist);
        }

        return $data;
    }

    /**
     * Build default attributes or merge with needed
     *
     * @param  array $attrs
     *
     * @return mixed
     */
    public function getAttrs($attrs = array())
    {
        $default = array(
            'align' => 'left',
        );

        if (empty($attrs)) {
            return $this->app->jbhtml->buildAttrs($default);
        }

        $merged = array_merge($default, $attrs);

        return $this->app->jbhtml->buildAttrs($merged);
    }

    /**
     * Build default styles or merge with needed
     *
     * @param  bool  $merge
     * @param  array $styles
     *
     * @return mixed
     */
    public function getStyles($styles = array(), $merge = false)
    {
        $default = array(
            'text-align'    => 'left',
            'border-bottom' => '1px solid #dddddd',
            'font-style'    => 'italic',
            'font-size'     => '12px',
            'color'         => '#000'
        );

        if (empty($styles)) {
            return $this->buildStyles($default);
        }

        if ($merge === true) {
            $styles = array_merge($default, $styles);
        }

        return $this->buildStyles($styles);
    }

    /**
     * Build styles from array
     *
     * @param  $styles
     *
     * @return string
     */
    public function buildStyles($styles)
    {
        $result = ' style="';

        if (is_string($styles)) {
            $result .= $styles;

        } elseif (!empty($styles)) {
            foreach ($styles as $key => $value) {

                if (!empty($value) || $value == '0' || $key == 'value') {
                    $result .= $key . ':' . $value . ';';
                }
            }
        }

        $result .= "\"";
        return JString::trim($result);
    }

    /**
     * @param         $text
     * @param  string $color - name|hex|rgb
     * @param  int    $size  - from 1 to 7
     *
     * @return string
     */
    public function fontColor($text, $color = '#000', $size = 2)
    {
        return '<i><font size="' . $size . '" color="' . $color . '">' . $text . '</font></i>';
    }

    /**
     * Default cart options
     * @return JSONData
     */
    protected function _getCartConfig()
    {
        $config = JBModelConfig::model();

        return $config->getGroup('cart.config');
    }

    /**
     * Get shipping service element from order
     *
     * @return JBCartElementShipping
     */
    protected function _getShipping()
    {
        return $this->_order->getShipping();
    }

    /**
     * Get payment service element from order
     *
     * @return JBCartElementPayment
     */
    protected function _getPayment()
    {
        return $this->_order->getPayment();
    }

    /**
     * Get shipping service element data from order
     *
     * @return JSONData
     */
    protected function _getShippingData()
    {
        return $this->_getShipping()->data();
    }

    /**
     * Get payment service element data from order
     *
     * @return JSONData
     */
    protected function _getPaymentData()
    {
        return $this->_getPayment()->data();
    }

    /**
     * Get related shipping fields data data from order
     *
     * @param  boolean $assoc - return array or JSONData
     *
     * @return JSONData|array
     */
    protected function _getShippingFieldsData($assoc = false)
    {
        $elements       = array();
        $shipping       = $this->_getShipping();
        $relatedfields  = $shipping->config->get('shippingfields', array());
        $shippingfields = $this->getOrder()->getShippingFields();

        if (!empty($relatedfields)) {
            foreach ($relatedfields as $relatedfield) {

                $relatedfield = JString::strtolower(JString::trim($relatedfield));

                if (empty($relatedfield)) {
                    continue;
                }
                $identifier = '_' . $relatedfield;

                $elements[$relatedfield] = $shippingfields->get($identifier);
            }
        }

        //return !empty($elements) && $assoc === true ? $elements->getArrayCopy() : $elements;
        return $elements;
    }

    /**
     * @param  string      $str
     * @param  bool|string $charlist
     *
     * @return mixed|string
     */
    private function _clean($str, $charlist = false)
    {
        $str = JString::trim($str, $charlist);
        $str = JString::strtolower($str);

        return $str;
    }

}

/**
 * Class JBCartElementNotificationException
 */
class JBCartElementEmailNotificationException extends JBCartElementException
{
}