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


/**
 * Class JBRequestHelper
 */
class JBRequestHelper extends AppHelper
{

    const ADMIN_FORM_KEY = 'jbzooform';

    /**
     * @var RequestHelper|JRequest
     */
    protected $_request = null;

    /**
     * @param App $app
     */
    public function __construct($app)
    {
        parent::__construct($app);

        $this->_request = $this->app->request;
    }

    /**
     * Clear and escape all values (recursive)
     * @param string|array $value
     * @return null|string
     */
    public function clear($value)
    {
        if (!is_array($value)) {

            $value = strip_tags($value);
            $value = JString::trim($value);

            // force clean input vars
            //$value = str_replace(array('"', "'", ';', '--', '`', '.', ','), ' ', $value);

            if (JString::strlen($value)) {
                return $value;
            }

        } else {

            foreach ($value as $key => $val) {
                $value[$key] = $this->clear($val);
            }

            return $value;
        }

        return null;
    }

    /**
     * Get variable from request
     * @param      $valueName
     * @param null $default
     * @return null|string
     */
    public function get($valueName, $default = null)
    {
        $jInput = JFactory::getApplication()->input;
        $value  = $jInput->get($valueName, $default, false);
        $value  = $this->clear($value);

        return $value;
    }

    /**
     * Gets the value of a user state variable.
     * @param   string $key     The key of the user state variable.
     * @param   string $request The name of the variable passed in a request.
     * @param   string $default The default value for the variable if not found. Optional.
     * @param   string $type    Filter for the variable, for valid values see {@link JFilterInput::clean()}. Optional.
     * @return  object  The request user state.
     * @throws \Exception
     */
    public function take($key, $request, $default = null, $type = 'none')
    {
        return JFactory::getApplication()->getUserStateFromRequest($key, $request, $default, $type);
    }

    /**
     * Get element name
     * @return mixed
     */
    public function getElement()
    {
        $element = str_replace('filterEl_', '', $this->get('element'));
        return $element;
    }

    /**
     * Get element
     * @return array
     */
    public function getElements()
    {
        static $result;

        if (!isset($result)) {

            $elements = $this->_request->get('e', 'array', array());
            $elements = $this->clear($elements);

            $result = array();
            foreach ($elements as $key => $value) {

                if (is_string($value) && strlen($value)) {
                    $result[$key] = $value;

                } elseif (is_array($value)) {

                    foreach ($value as $valueRow) {
                        if (!empty($valueRow)) {
                            $result[$key] = $value;
                            break;
                        }
                    }

                }
            }
        }

        return $result;
    }

    /**
     * Check if is current request method is POST
     * @return bool
     */
    public function isPost()
    {
        return 'POST' == strtoupper(JFactory::getApplication()->input->getMethod(false, false));
    }

    /**
     * Check, is current request - ajax
     * @return bool
     */
    public function isAjax()
    {
        if (function_exists('apache_request_headers')) {

            $headers = apache_request_headers();
            foreach ($headers as $key => $value) {
                if (strToLower($key) == 'x-requested-with' && strToLower($value) == 'xmlhttprequest') {
                    return true;
                }
            }

        } else if (isset($_SERVER['HTTP_X_REQUESTED_WITH'])
            && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest'
        ) {
            return true;
        }

        return false;
    }

    /**
     * Check request value
     * @param $requestKey string
     * @param $value      string
     * @return bool
     */
    public function is($requestKey, $value)
    {
        return strtolower($this->get($requestKey, null)) == strtolower($value);
    }

    /**
     * Get file
     * @param      $fieldName
     * @param null $group
     * @return array|null
     */
    public function getFile($fieldName, $group = null)
    {
        $result = array();

        if ($group && isset($_FILES[$group])) {
            if (isset($_FILES[$group]['name']) && is_array($_FILES[$group]['name'])) {

                foreach ($_FILES[$group] as $key => $value) {
                    if (isset($value[$fieldName])) {
                        $result[$key] = $value[$fieldName];
                    }
                }

                return $result;
            } else {
                return isset($_FILES[$fieldName]) ? $_FILES[$fieldName] : null;
            }

        } else {
            return isset($_FILES[$fieldName]) ? $_FILES[$fieldName] : null;
        }

    }

    /**
     * Get array from request
     * @param string $arrayName
     * @param array  $default
     * @return array
     */
    public function getArray($arrayName, $default = array())
    {
        $result = $this->_request->get($arrayName, 'array');

        if (is_null($result)) {
            return $default;
        }

        return $result;
    }

    /**
     * Get request from Control Panel form
     * @param array $default
     * @return array
     */
    public function getAdminForm($default = array())
    {
        return $this->getArray(self::ADMIN_FORM_KEY, $default);
    }


    /**
     * Set request value
     * @param $key
     * @param $value
     * @return mixed
     */
    public function set($key, $value)
    {
        return $this->_request->set($key, $value);
    }

    /**
     * Get request value as word
     * @param      $varName
     * @param null $default
     * @return string
     */
    public function getWord($varName, $default = null)
    {
        $value = $this->get($varName, $default);
        $value = strtolower((string)preg_replace('/[^A-Z\_\-]/i', '', $value));

        return $value;
    }

    /**
     * Get current controller name (HACK)
     * @param string $default
     * @return mixed
     */
    public function getCtrl($default = 'default')
    {
        return str_replace('jbuniversal', '', $this->getWord('controller', $default));
    }

    /**
     * Check equel controller with word (HASK)
     * @param $check
     * @return bool
     */
    public function isCtrl($check)
    {
        return $this->getCtrl('') === strtolower($check);
    }

    /**
     * @param string $type
     * @param int    $default
     * @return int
     */
    public function getSystem($type, $default = null)
    {
        $menuParam = $requestVar = null;
        if ($type == 'item') {
            $requestVar = 'item_id';
            $menuParam  = 'item_id';

        } else if ($type == 'category') {

            if ($this->is('task', 'filter')) {
                $elements = $this->getElements();
                if (isset($elements['_itemcategory'])) {
                    if (is_array($elements['_itemcategory'])) {
                        reset($elements['_itemcategory']);
                        return (int)current($elements['_itemcategory']);
                    } else {
                        return (int)$elements['_itemcategory'];
                    }
                }
            }

            $requestVar = 'category_id';
            $menuParam  = 'category';

        } else if ($type == 'app') {
            $requestVar = 'app_id';
            $menuParam  = 'application';
        }

        if (empty($requestVar)) {
            return $default;
        }

        $varId = (int)$this->get($requestVar);
        if ($varId > 0) {
            return $varId;
        }

        $activeMenu = JFactory::getApplication()->getMenu()->getActive();
        $result     = 0;
        if ($activeMenu && $activeMenu->params) {
            $result = (int)$activeMenu->params->get($menuParam);
        }

        if (empty($result)) {
            $result = (int)$default;
        }

        return $result;
    }

    /**
     * @param string $default
     * @return string
     */
    public function  getCurrency($default = null)
    {
        $key = 'JBZooCurrencyToggle_current';

        if (empty($default)) {
            $default = JBModelConfig::model()->getCurrency();
        }

        $currency = isset($_COOKIE[$key]) ? $_COOKIE[$key] : $default; // TODO use Joomla API
        $currency = $this->app->jbvars->currency($currency);

        if ($currency == JBCartValue::PERCENT) {
            return null;
        }

        return $currency ? $currency : $default;
    }

}
