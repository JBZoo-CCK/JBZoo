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
 * Class JBCartElementPriceSku
 */
class JBCartElementPriceSku extends JBCartElementPrice
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
        $value = JString::trim($this->getValue());
        if (JString::strlen($value) > 0) {
            return true;
        }

        return false;
    }

    /**
     * Get elements search data
     * @return mixed|null
     */
    public function getSearchData()
    {
        $value = $this->getValue();

        return $value;
    }

    /**
     * @param array $params
     * @return mixed|null|string
     */
    public function edit($params = array())
    {
        if ($layout = $this->getLayout('edit.php')) {
            return self::renderEditLayout($layout);
        }

        return null;
    }

    /**
     * @param array $params
     *
     * @return array|mixed|null|string
     */
    public function render($params = array())
    {
        if ($layout = $this->getLayout()) {
            return self::renderLayout($layout, array(
                'value'  => $this->getValue(),
                'params' => $params
            ));
        }

        return null;
    }

    /**
     * Returns data when variant changes
     * @return null
     */
    public function renderAjax()
    {
        $params = $this->getRenderParams();

        return $this->render($params);
    }

    /**
     * Get elements value
     * @param string $key
     * @param null   $default
     * @return mixed|null
     */
    public function getValue($key = 'value', $default = null)
    {
        $value = parent::getValue($key, $default);
        if (empty($value)) {
            $value = $this->getJBPrice()->getItem()->id;
        }

        return $value;
    }

}
