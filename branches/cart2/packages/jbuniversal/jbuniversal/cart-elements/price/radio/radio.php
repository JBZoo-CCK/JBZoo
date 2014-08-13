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
 * Class JBCartElementPriceRadio
 */
class JBCartElementPriceRadio extends JBCartElementPrice
{

    /**
     * @param array $param
     * @return mixed|null|string
     */
    public function edit($param = array())
    {
        $params = $this->getParams();

        $param  = $this->app->data->create($param);
        if ($layout = $this->getLayout('edit.php')) {
            return self::renderLayout($layout, array(
                'params'  => $params,
                'param'   => $param,
                'options' => $this->_renderOptions()
            ));
        }

        return null;
    }

    /**
     * @param array $params
     * @return array|mixed|null|string|void
     */
    public function render($params = array())
    {
        $params   = $this->app->data->create($params);
        $options  = $this->getAllOptions();
        $template = $params->get('template', 'radio');

        if ($layout = $this->getLayout($template . '.php')) {
            return self::renderLayout($layout, array(
                'params'  => $params,
                'options' => $options
            ));
        }

        return null;
    }

    /**
     * @param null $identifier
     * @param $name
     * @param int $index
     * @return string
     */
    public function getParamName($identifier = null, $name, $index = 0)
    {
        if (empty($identifier)) {
            $identifier = $this->identifier;
        }

        return "elements[{$identifier}][variations][{$index}][params][{$this->identifier}][{$name}]";
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

}
