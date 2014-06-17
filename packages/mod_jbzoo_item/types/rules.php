<?php
/**
 * JBZoo App is universal Joomla CCK, application for YooTheme Zoo component
 *
 * @package     jbzoo
 * @version     2.x Pro
 * @author      JBZoo App http://jbzoo.com
 * @copyright   Copyright (C) JBZoo.com,  All rights reserved.
 * @license     http://jbzoo.com/license-pro.php JBZoo Licence
 * @coder       Vitaliy Yanovskiy <joejoker@jbzoo.com>
 * @coder       Oganov Alexander <t_tapakm@yahoo.com>
 */

// no direct access
defined('_JEXEC') or die('Restricted access');


/**
 * Class JBZooModItemRuleText
 */
class JBZooModItemRuleText
{
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

        if (preg_match("/[0-9]/", $value)) {
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
 * Class JBZooModItemRulePriceadvance
 */
class JBZooModItemRuleJBPriceAdvance extends JBZooModItemRuleText
{
    /**
     * @param $key
     * @param $value
     * @return array
     */
    public function validateValues($key, $value)
    {
        $result   = array();
        $userCurr = false;
        $app      = App::getInstance('zoo');
        $elements = $app->jbentity->getItemTypesData(false);
        $element  = $elements[$key];

        unset($elements);

        $result[$key]['currency'] = $element['currency_default'];

        if (preg_match('#(.*)([a-z]{3})$#i', $value, $curr) && !$this->_getFlag($value)) {

            list($empty, $value, $userCurr) = $curr;
            unset($empty);
            $value    = JString::trim($value);
            $userCurr = $app->jbmoney->checkCurrency($userCurr);
        }

        if (strpos($value, '/')) {
            $result[$key]['range'] = $value;

        } elseif (is_numeric($value)) {
            $result[$key]['val'] = $app->jbmoney->clearValue($value);

        } else {
            $result[$key] = $this->_getFlag($value);

        }

        if ($userCurr) {
            $result[$key]['currency'] = $userCurr;
        }

        return $result;
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
        if ($value == '1') {
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