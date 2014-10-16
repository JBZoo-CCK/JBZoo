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
        if ($layout = $this->getLayout('edit.php')) {
            return self::renderLayout($layout);
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

        //$params->get('template', 'bool') . '.php')
        if ($layout = $this->getLayout('select.php')) {
            return self::renderLayout($layout, array(
                'params' => $params
            ));
        }

        return NULL;
    }

}
