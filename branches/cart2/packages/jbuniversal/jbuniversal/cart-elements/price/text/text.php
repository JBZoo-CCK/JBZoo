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
     * @return mixed|void
     */
    public function edit()
    {
        $value  = $this->app->data->create($this->getValue($this->identifier));
        $html   = array();
        $html[] = $this->app->html->_('control.text', $this->getName(), $value->get('value'));

        return implode($html);
    }

    /**
     * @param array $params
     * @return array|mixed|null|string|void
     */
    public function render($params = array())
    {
        $params = $this->app->data->create($params);
        $data   = array();

        $template = $params->get('template', 'radio');

        foreach ($this->getAllData() as $value) {
            $data[] = $value['value'];
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
