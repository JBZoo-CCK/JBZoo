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
 * Class JBCartElementNotification
 */
abstract class JBCartElementNotification extends JBCartElement
{
    /**
     * @var JBCartPositionHelper
     */
    protected $_position;

    /**
     * Event Subject
     * @var Object
     */
    protected $_subject;

    /**
     * Default cart options
     * @var JSONData
     */
    protected $_cartConfig;

    /**
     * @var array
     */
    protected $_secondaryMacros = array(
        '{date}'         => '',
        '{order_id}'     => '',
        '{order_status}' => '',
        '{sitename}'     => '',
        '{user_id} '     => '',
        '{username}'     => '',
        '{created_by}'   => ''
    );

    /**
     * @var array
     */
    protected $_primaryMacros = array(
        '{order_title}' => '',
        '{sitelink}'    => '',
        '{shopname}'    => ''
    );

    /**
     * @var array
     */
    protected $_macros = array();

    /**
     * Name of config
     * @var string
     */
    protected $_namespace = JBCart::ELEMENT_TYPE_NOTIFICATION;

    /**
     * Class constructor
     * @param App    $app
     * @param string $type
     * @param string $group
     */
    public function __construct($app, $type, $group)
    {
        parent::__construct($app, $type, $group);

        $this->_position   = $this->app->jbcartposition;
        $this->_cartConfig = $this->_getCartConfig();
    }

    /**
     * Launch notification
     * @return void
     */
    abstract function notify();

    /**
     * @param $subject
     * @return $this
     */
    public function setSubject($subject)
    {
        $this->_subject = $subject;

        if (get_class($subject) == 'JBCartOrder') {
            $this->setOrder($subject);
        }

        return $this->setMacrosValues();
    }

    /**
     * @return Object
     */
    public function getSubject()
    {
        return $this->_subject;
    }

    /**
     * @param  string $html
     * @return string
     */
    public function replace($html)
    {
        foreach ($this->_macros as $macros => $value) {
            $html = JString::str_ireplace($macros, $value, $html);
        }

        return $html;
    }


    /**
     * Replace macros with values in config
     * @return $this
     */
    public function replaceConfig()
    {
        $config = $this->config->getArrayCopy();

        if (!empty($config)) {
            foreach ($config as $key => $value) {

                if (is_array($value)) {
                    continue;
                }

                $config[$key] = $this->replace($value);
            }

            $this->setConfig($config);

        }

        return $this;
    }

    /**
     * Decoding the result of API call
     * @param $responseBody
     * @return mixed
     */
    public function processingData($responseBody)
    {
        return $responseBody;
    }

    /**
     * Make request to service and get results
     * @param  string $url    - Shipping service url.
     * @param  string $method - POST, GET.
     * @param  array  $data   - Data for POST $method
     * @return bool|array
     */
    protected function _callService($url, $method = 'get', $data = array())
    {
        $response = $this->app->jbhttp->request($url, $data, array(
            'method' => $method,
            'cache'  => 0,
        ));

        $responseData = $this->processingData($response);

        return $responseData;
    }

    /**
     * Set values macros
     * @return $this
     */
    public function setMacrosValues()
    {
        $this
            ->_setSecondaryMacros()
            ->_setPrimaryMacros();

        return $this;
    }

    /**
     * @return $this
     */
    protected function _setPrimaryMacros()
    {
        $sitename = JFactory::getConfig()->get('sitename');
        $sitelink = '<a title="' . $sitename . '" href="' . JUri::base() . '" >' . JUri::base() . '</a>';

        $this->_primaryMacros = array_merge($this->_primaryMacros, array(
                '{order_title}' => $this->config->get('title'),
                '{sitelink}'    => $sitelink,
                '{shopname}'    => $this->_cartConfig->get('shop_name'),
            )
        );

        $this->_macros = array_merge($this->_macros, $this->_primaryMacros);
        $this->replaceConfig();

        return $this;
    }

    /**
     * @return $this
     */
    protected function _setSecondaryMacros()
    {
        $order = $this->getOrder();
        $guest = JText::_('JBZOO_ORDER_CREATED_BY_GUEST');
        $user  = JFactory::getUser();

        $sitename   = JFactory::getConfig()->get('sitename');
        $order_id   = $order ? $order->id : '';
        $status     = $order ? $order->getStatus()->getName() : '';
        $created_by = $order ? JFactory::getUser($order->created_by)->username : $guest;

        $username = $user->get('username');
        $username = $username === null && $created_by === null ? $guest : $created_by;

        $this->_secondaryMacros = array_merge($this->_secondaryMacros, array(
            '{date}'         => date('Y-m-d H:m'),
            '{order_id}'     => $order_id,
            '{order_status}' => $status,
            '{sitename}'     => $sitename,
            '{user_id} '     => $user->get('id', $guest),
            '{username}'     => $username,
            '{created_by}'   => $created_by
        ));

        $this->_macros = array_merge($this->_secondaryMacros, $this->_secondaryMacros);
        $this->replaceConfig();

        return $this;
    }

    /**
     * Cleans data
     * @param  string         $data
     * @param  string|boolean $charlist
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
     * Default cart options
     * @return JSONData
     */
    protected function _getCartConfig()
    {
        $config = JBModelConfig::model();

        return $config->getGroup('cart.config');
    }

    /**
     * @param  string      $str
     * @param  bool|string $charlist
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
class JBCartElementNotificationException extends JBCartElementException
{
}
