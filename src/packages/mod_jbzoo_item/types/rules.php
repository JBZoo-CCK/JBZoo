<?php
/**
 * JBZoo Application
 *
 * This file is part of the JBZoo CCK package.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package    Application
 * @license    GPL-2.0
 * @copyright  Copyright (C) JBZoo.com, All rights reserved.
 * @link       https://github.com/JBZoo/JBZoo
 * @author     Denis Smetannikov <denis@jbzoo.com>
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

/**
 * Class JBZooModItemRuleText
 */
class JBZooModItemRuleText
{
    /**
     * @type App
     */
    public $app = null;

    /**
     *
     */
    function __construct()
    {
        $this->app = App::getInstance('zoo');
    }

    /**
     * @param $key
     * @param $value
     * @return array
     */
    public function validateValues($key, $value)
    {
        if (!empty($value)) {
            if (strpos($value, '/')) {
                $result[$key] = array('range' => $value);
            } else {
                $result[$key] = $value;
            }

            return $result;
        }

        return false;
    }
}

/**
 * Class JBZooModItemRuleRating
 */
class JBZooModItemRuleRating
{
    /**
     * @param $key
     * @param $value
     * @return array
     */
    public function validateValues($key, $value)
    {
        if (!empty($value)) {
            return array($key => $value);
        }

        return false;
    }
}

/**
 * Class JBZooModItemRuleItemCategory
 */
class JBZooModItemRuleItemCategory extends JBZooModItemRuleText
{
    /**
     * @param $key
     * @param $value
     * @return array
     */
    public function validateValues($key, $value)
    {
        $app    = App::getInstance('zoo');
        $result = array();

        if (is_numeric($value)) {
            $result[$key] = $value;
        } else {
            $slug = $app->string->sluggify($value);
            $cat  = JBModelCategory::model()->getByAlias($slug);
            if (!empty($cat)) {
                $result[$key] = $cat->id;
            }

        }

        return !empty($result) ? $result : false;
    }
}

/**
 * Class JBZooModItemRuleItemDate
 */
class JBZooModItemRuleItemDate extends JBZooModItemRuleText
{
    /**
     * @param $key
     * @param $value
     * @return mixed
     */
    public function validateValues($key, $value)
    {
        if (strpos($value, '/')) {
            $result[$key]['range'] = explode('/', $value);
        } else {
            $result[$key] = $value;
        }

        return $result;
    }

}

/**
 * Class JBZooModItemRuleDate
 */
class JBZooModItemRuleDate extends JBZooModItemRuleItemDate
{
    /**
     * @param $key
     * @param $value
     * @return mixed
     */
    public function validateValues($key, $value)
    {
        if (strpos($value, '/')) {
            $result[$key]['range-date'] = explode('/', $value);
        } else {
            $result[$key] = $value;
        }

        return $result;
    }
}

/**
 * Class JBZooModItemRulePrice
 */
class JBZooModItemRuleJBPrice extends JBZooModItemRuleText
{
    /**
     * @var App
     */
    public $app;

    /**
     * Array of element config
     * @var array
     */
    protected $_element = array();

    /**
     * UUID of element
     * @var string|null
     */
    protected $_element_id = null;

    /**
     * UUID of price element
     * @var string|null
     */
    protected $_param_id = null;

    /**
     * @param $elem_id
     * @param $param_id
     * @param $value
     * @return array
     */
    public function validateElements($elem_id, $param_id, $value)
    {
        $params    = null;
        $this->app = App::getInstance('zoo');
        $elements  = $this->app->jbentity->getItemTypesData(false);

        $this->_element    = $elements[$elem_id];
        $this->_element_id = $elem_id;
        $this->_param_id   = $param_id;
        $result[$elem_id]  = array();

        $value = JString::trim($value);

        unset($elements);
        if ($param_id == '_value') {
            $params[$param_id] = $this->_validateValue($value);

        } elseif ($param_id == '_balance') {
            $params[$param_id] = $this->_validateBalance($value);

        } elseif (in_array($param_id, array('_image', '_discount'))) {
            $params[$param_id] = $this->_validateBool($value);

        } elseif ($this->_validateDate($value)) {
            $params[$param_id] = $this->_validateDate($value);

        } else {
            $params[$param_id] = $this->_validateDefault($value);

        }

        if (isset($params[$param_id])) {
            $result[$elem_id] = $params;
        }

        return $result;
    }

    /**
     * @param $value
     * @return int|string
     */
    protected function _validateValue($value)
    {
        $result = array();
        if (strpos($value, '/')) {
            $result['range'] = $value;

        } elseif (is_numeric($value)) {
            $result['value'] = $this->app->jbmoney->clearValue($value);

        }

        return $result;
    }

    /**
     * @param $value
     * @return mixed
     */
    protected function _validateBalance($value)
    {
        return $value;
    }

    /**
     * @param $value
     * @return int
     */
    protected function _validateBool($value)
    {
        return (int)$value;
    }

    /**
     * @param $date
     * @return mixed
     */
    protected function _validateDate($date)
    {
        $result = array();

        if (strpos($date, '/')) {
            list($from, $to) = explode('/', $date);

            if ($this->_isDate($from) && $this->_isDate($to)) {
                $result = array($from, $to);
            }

        } else {
            if ($this->_isDate($date)) {
                $result[] = $date;
            }
        }

        return empty($result) ? false : $result;
    }

    /**
     * Check if value seems like date
     * @param string $date
     * @param string $format
     * @return bool
     */
    protected function _isDate($date, $format = 'Y-m-d')
    {
        $dateObj = DateTime::createFromFormat($format, $date);

        return $dateObj && $dateObj->format($format) == $date;
    }

    /**
     * @param $value
     * @return mixed
     */
    protected function _validateDefault($value)
    {
        return $value;
    }

    /**
     * @param $exVal
     * @return mixed
     */
    protected function _getFlag($exVal)
    {
        $result = array();

        if (!is_array($exVal)) {
            $exVal = (array)$exVal;
        }

        foreach ($exVal as $val) {

            if (strpos($val, '/')) {
                $result['range'] = $val;

            } elseif ($flags = $this->_getOptions($val)) {
                $result = array_merge($result, $flags);

            } elseif (is_numeric($val)) {
                $result['val'] = $val;

            }

        }

        return $result;
    }

    /**
     * @param $val
     * @return null | array
     */
    protected function _getOptions($val)
    {
        $result = null;

        if (trim($val) == 'balance') {
            $result['balance'] = 1;

        } elseif (trim($val) == 'hit') {
            $result['hit'] = 1;

        } elseif (trim($val) == 'new') {
            $result['new'] = 1;

        } elseif (trim($val) == 'sale') {
            $result['sale'] = 1;
        }

        return $result;
    }

}

class JBZooModItemRuleJBPricePlain extends JBZooModItemRuleJBPrice
{

}

class JBZooModItemRuleJBPriceCalc extends JBZooModItemRuleJBPrice
{

}

/**
 * Class JBZooModItemRuleItemFrontPage
 */
class JBZooModItemRuleItemFrontPage extends JBZooModItemRuleText
{

    /**
     * @param $key
     * @param $value
     * @return mixed
     */
    public function validateValues($key, $value)
    {
        if (!empty($value) || $value === '0') {
            $result[$key] = $value;

            return $result;
        } else {
            return false;
        }
    }
}


/**
 * Class JBZooModItemRuleJBSelectCascade
 */
class JBZooModItemRuleJBSelectCascade extends JBZooModItemRuleText
{

    /**
     * @param $key
     * @param $value
     * @return array
     */
    public function validateValues($key, $value)
    {
        if (strpos($value, '||')) {
            $exVal = explode('||', $value);
        } else {
            $exVal = $value;
        }

        $result[$key] = $exVal;

        return $result;
    }
}

/**
 * Class JBZooModItemRuleJBImage
 */
class JBZooModItemRuleJBImage extends JBZooModItemRuleText
{
    /**
     * @param $key
     * @param $value
     * @return string
     */
    public function validateValues($key, $value)
    {
        if ($this->app->jbvars->bool($value)) {
            $value = JBModelElementJBImage::IMAGE_EXISTS;
        } elseif ($value != '' && $value == '0') {
            $value = JBModelElementJBImage::IMAGE_NO_EXISTS;
        }

        if (!empty($value)) {
            $result[$key] = $value;

            return $result;
        }

        return false;
    }
}

/**
 * Class JBZooModItemRuleImage
 */
class JBZooModItemRuleImage extends JBZooModItemRuleText
{

    /**
     * @param $key
     * @param $value
     * @return string
     */
    public function validateValues($key, $value)
    {
        if (!empty($value)) {
            $result[$key] = $value;

            return $result;
        }

        return null;
    }
}

/**
 * Class JBZooModItemRuleItemAuthor
 */
class JBZooModItemRuleItemAuthor extends JBZooModItemRuleText
{
    /**
     * @param $key
     * @param $value
     * @return array
     */
    public function validateValues($key, $value)
    {
        if (!empty($value)) {
            $result[$key] = $value;

            return $result;
        }

        return null;
    }
}

/**
 * Class JBZooModItemRuleItemName
 */
class JBZooModItemRuleItemName extends JBZooModItemRuleText
{
    /**
     * @param $key
     * @param $value
     * @return array
     */
    public function validateValues($key, $value)
    {
        if (!empty($value)) {
            $result[$key] = $value;

            return $result;
        }

        return null;
    }
}

/**
 * Class JBZooModItemRuleItemTag
 */
class JBZooModItemRuleItemTag extends JBZooModItemRuleText
{
    /**
     * @param $key
     * @param $value
     * @return array
     */
    public function validateValues($key, $value)
    {
        if (!empty($value)) {
            $result[$key] = $value;

            return $result;
        }

        return null;
    }
}