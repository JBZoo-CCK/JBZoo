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
 * Class JBCartElementPriceDescription
 */
class JBCartElementPriceDescription extends JBCartElementPrice
{
    /**
     * If elements value is set
     * @param  array $params
     * @return bool|void
     */
    public function hasValue($params = array())
    {
        return $this->getBasicData();
    }

    /**
     * @param array $params
     * @return mixed|null|string
     */
    public function edit($params = array()) {

        $params = $this->getParams();

        if($layout = $this->getLayout('edit.php')) {
            return self::renderLayout($layout, array(
                'params' => $params
            ));
        }

        return null;
    }

    /**
     * @param  array $params
     * @return array|mixed|null|string
     */
    public function render($params = array())
    {
        $params = $this->app->data->create($params);
        if ($layout = $this->getLayout()) {
            return self::renderLayout($layout, array(
                'params' => $params
            ));
        }

        return null;
    }
}
