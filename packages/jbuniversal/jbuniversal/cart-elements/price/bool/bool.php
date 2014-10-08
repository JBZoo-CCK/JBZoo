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
 * Class JBCartElementPriceBool
 */
class JBCartElementPriceBool extends JBCartElementPrice
{
    /**
     * @param  array $param
     *
     * @return bool
     */
    public function hasFilterValue($param = array())
    {
        return FALSE;
    }

    /**
     * @return mixed|null|string
     */
    public function edit()
    {
        $params = $this->getParams();

        if ($layout = $this->getLayout('edit.php')) {
            return self::renderLayout($layout, array(
                'params' => $params
            ));
        }

        return NULL;
    }

    /**
     * @param array $params
     *
     * @return array|mixed|null|string
     */
    public function render($params = array())
    {
        $params = $this->app->data->create($params);

        if ($layout = $this->getLayout($params->get('template', 'bool') . '.php')) {
            return self::renderLayout($layout, array(
                'params' => $params
            ));
        }

        return NULL;
    }

}
