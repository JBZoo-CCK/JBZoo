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
 * Class JBCartElementPriceMargin
 */
class JBCartElementPriceMargin extends JBCartElementPrice
{
    /**
     * Check if element has value
     *
     * @param array $params
     *
     * @return bool
     */
    public function hasValue($params = array())
    {
        $value = $this->getValue();
        if ($value->isEmpty() || $this->options('isOverlay')) {
            return false;
        }

        return true;
    }

    /**
     * @param  array $params
     * @return bool
     */
    public function hasFilterValue($params = array())
    {
        return false;
    }

    /**
     * @param array $params
     * @return mixed|string
     */
    public function edit($params = array())
    {
        $layout    = 'edit';
        $isOverlay = $this->options('isOverlay');
        if ($isOverlay) {
            $layout = 'disabled';
        }

        if ($layout = $this->getLayout($layout . '.php')) {
            return self::renderEditLayout($layout, array(
                'margin'  => $this->getValue(),
                'message' => JText::sprintf('JBZOO_JBPRICE_CALC_PARAM_CANT_USE', '<strong>' . $this->getElementType() . '</strong>')
            ));
        }

        return null;
    }

    /**
     * @param  array $params
     *
     * @return array|mixed|null|string
     */
    public function render($params = array())
    {
        if ($layout = $this->getLayout()) {
            return self::renderLayout($layout, array(
                'margin' => $this->getValue()
            ));
        }

        return null;
    }

    /**
     * Get elements value
     *
     * @param string $key
     * @param null   $default
     *
     * @internal param string $key
     * @return mixed|null
     */
    public function getValue($key = 'value', $default = null)
    {
        $value = parent::getValue($key, $default);

        return JBCart::val($value)->abs();
    }

    /**
     * Returns data when variant changes
     * @param array $params
     * @return null
     */
    public function renderAjax($params = array())
    {
        return $this->render($params);
    }

}
