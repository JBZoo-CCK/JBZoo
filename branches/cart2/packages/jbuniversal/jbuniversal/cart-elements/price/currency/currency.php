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
 * Class JBCartElementPriceCurrency
 */
class JBCartElementPriceCurrency extends JBCartElementPrice
{
    /**
     * @return mixed|null
     */
    public function edit()
    {
        return NULL;
    }

    /**
     * @param array $params
     *
     * @return bool
     */
    public function hasFilterValue($params = array())
    {
        return FALSE;
    }

    /**
     * @param array $params
     *
     * @return array|mixed|null|string
     */
    public function render($params = array())
    {
        $params   = $this->app->data->create($params);
        $template = $params->get('template', 'currency');

        $list    = $params->get('currency_list', array());
        $default = $params->get('currency_default', 'EUR');

        if ($layout = $this->getLayout($template . '.php')) {
            return self::renderLayout($layout, array(
                'params'  => $params,
                'list'    => $list,
                'default' => $default
            ));
        }

        return NULL;
    }

}
