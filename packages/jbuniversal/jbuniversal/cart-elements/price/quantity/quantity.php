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
     * @param array $params
     * @return mixed|null|string
     */
    public function edit($params = array())
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
        if ($layout = $this->getLayout()) {
            return self::renderLayout($layout, array(
                'params' => $this->interfaceParams()
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
            'min'      => $params->get('min', 1),
            'max'      => $params->get('max', 9),
            'step'     => $params->get('step', 1),
            'default'  => $params->get('default', 1),
            'decimals' => $params->get('decimals', 0)
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
        $this->app->jbassets->quantity();
        self::addToStorage(array(
            'jbassets:css/libraries.css',
            'jbassets:less/widget/quantity.less',
            'jbassets:js/widget/quantity.js',
            'cart-elements:price/quantity/assets/js/quantity.js'
        ));

        return parent::loadAssets();
    }
}
