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
 * Class JBCartElementPriceSelect
 */
class JBCartElementPriceSelect extends JBCartElementPrice
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
        $value = $this->getOptions();

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
     * @return mixed|null|string
     */
    public function edit()
    {
        if ($layout = $this->getLayout('edit.php')) {
            return self::renderLayout($layout, array(
                'options' => $this->parseOptions()
            ));
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
        $data     = $this->getOptions();

        if ($layout = $this->getLayout($template . '.php')) {
            return self::renderLayout($layout, array(
                'params' => $params,
                'data'   => $data
            ));
        }

        return NULL;
    }

}
