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
 * Class JBCartElementPriceQuantity
 */
class JBCartElementPriceQuantity extends JBCartElementPrice
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
        return true;
    }

    /**
     * @return mixed|null|string
     */
    public function edit()
    {
        return null;
    }

    /**
     * @param array $params
     *
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

    /**
     * Get params for widget
     * @return array
     */
    public function interfaceParams()
    {
        $params = $this->getRenderParams();

        return array(
            'min'      => (float)$params->get('min', 1),
            'max'      => (float)$params->get('max', 9),
            'step'     => (float)$params->get('step', 1),
            'default'  => (float)$params->get('default', 1),
            'decimals' => (float)$params->get('decimals', 0)
        );
    }

    /**
     * Returns data when variant changes
     * @return null
     */
    public function renderAjax()
    {
        return array();
    }

    /**
     * Load elements css/js assets
     * @return $this
     */
    public function loadAssets()
    {
        return parent::loadAssets();
    }

}
