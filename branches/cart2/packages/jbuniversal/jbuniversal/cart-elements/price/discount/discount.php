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
 * Class JBCartElementPriceDiscount
 */
class JBCartElementPriceDiscount extends JBCartElementPrice
{
    /**
     * @param  array $param
     * @return bool|void
     */
    public function hasFilterValue($param = array())
    {
        return false;
    }

    /**
     * @return mixed|string
     */
    public function edit()
    {
        $params = $this->getParams();

        if ($layout = $this->getLayout('edit.php')) {

            return self::renderLayout($layout, array(
                'params' => $params,
            ));
        }

        return null;
    }

    /**
     * @param  array $params
     * @return array|mixed|null|string
     */
    public function render($params = array())
    {
        $params   = $this->app->data->create($params);
        $discount = $this->getBasic('_discount');

        if ($layout = $this->getLayout()) {
            return self::renderLayout($layout, array(
                'params'   => $params,
                'base'     => $this->getPrices(),
                'discount' => array(
                    'value'  => $discount['value'],
                    'format' => $this->app->jbmoney->toFormat($discount['value'], $discount['currency'])
                ),
                'mode'     => $params->get('sale_show', ElementJBPriceAdvance::SALE_VIEW_ICON_VALUE)
            ));
        }

        return null;
    }

    /**
     * @param null $identifier
     * @param $name
     * @return string
     */
    public function getBasicName($identifier = null, $name)
    {
        if (empty($identifier)) {
            $identifier = $this->identifier;
        }

        return "elements[{$identifier}][basic][params][{$this->identifier}][{$name}]";
    }

    /**
     * @param null $identifier
     * @param $name
     * @param  int $index
     * @return string
     */
    public function getParamName($identifier = null, $name, $index = 0)
    {
        if (empty($identifier)) {
            $identifier = $this->identifier;
        }

        return "elements[{$identifier}][variations][{$index}][params][{$this->identifier}][{$name}]";
    }

}
