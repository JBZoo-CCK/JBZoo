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
     * @return mixed|null|string
     */
    public function edit()
    {
        $params = $this->getParams();
        if ($layout = $this->getLayout('edit.php')) {
            return self::renderLayout($layout, array(
                'params'  => $params,
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
        $params  = $this->app->data->create($params);
        $data    = array();
        $options = $this->_renderOptions();
        $default = $this->getDefaultVariantData(true);

        $template = $params->get('template', 'radio');

        $i = 0;
        foreach ($this->getAllData() as $value) {
            if ($options[$i]['value'] == $value['value']) {
                $data[$options[$i]['value']] = $options[$i]['name'];
            }

            $i++;
        }

        if ($layout = $this->getLayout($template . '.php')) {
            return self::renderLayout($layout, array(
                'params' => $params,
                'data'   => $data
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
