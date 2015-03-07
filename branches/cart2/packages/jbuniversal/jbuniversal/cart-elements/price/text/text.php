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
 * Class JBCartElementPriceText
 */
class JBCartElementPriceText extends JBCartElementPrice
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
        $value = $this->getOptions(false);
        if (!empty($value)) {
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
     * @return array|mixed|null|string|void
     */
    public function render($params = array())
    {
        $template = $params->get('template', 'radio');

        if ($layout = $this->getLayout($template . '.php')) {
            return self::renderLayout($layout, array(
                'data' => $this->getOptions()
            ));
        }

        return null;
    }

    /**
     * Get elements value
     * @param string $key      Array key.
     * @param mixed  $default  Default value if data is empty.
     * @param bool   $toString A string representation of the value.
     * @return mixed|string
     */
    public function getValue($toString = false, $key = 'value', $default = null)
    {
        return $this->get($key, $default);
    }

}
