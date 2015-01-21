<?php
/**
 * JBZoo App is universal Joomla CCK, application for YooTheme Zoo component
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
 * Class JBCartElementPriceProperties
 */
class JBCartElementPriceProperties extends JBCartElementPrice
{
    /**
     * Check if element has value
     * @param array $params
     * @return bool
     */
    public function hasValue($params = array())
    {
        return false;
    }

    /**
     * Render element in jbprice admin
     * @param array $params
     * @return bool|mixed|string
     */
    public function edit($params = array())
    {
        if ($layout = $this->getLayout('edit.php')) {
            return self::renderEditLayout($layout);
        }

        return false;
    }

    /**
     * @param array $params
     * @return array|mixed|null|string|void
     */
    public function render($params = array())
    {
        $template = $params->get('template', 'radio');
        $data     = $this->getOptions();

        if ($layout = $this->getLayout($template . '.php')) {
            return self::renderLayout($layout, array(
                'params' => $params,
                'data'   => $data
            ));
        }

        return null;
    }

    /**
     * Get elements value
     * @param string $key
     * @param null   $default
     * @return mixed|null
     */
    public function getValue($key = 'height', $default = null)
    {
        return parent::getValue($key, $default);
    }

}
