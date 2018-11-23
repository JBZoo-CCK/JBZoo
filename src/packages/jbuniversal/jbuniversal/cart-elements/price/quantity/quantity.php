<?php
/**
 * JBZoo Application
 *
 * This file is part of the JBZoo CCK package.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package    Application
 * @license    GPL-2.0
 * @copyright  Copyright (C) JBZoo.com, All rights reserved.
 * @link       https://github.com/JBZoo/JBZoo
 * @author     Denis Smetannikov <denis@jbzoo.com>
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
     * @param array $params
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
     * @return array|mixed|null|string
     */
    public function render($params = array())
    {
        if ($layout = $this->getLayout()) {
            return self::renderLayout($layout, array(
                'params' => $this->app->data->create($this->interfaceParams($params))
            ));
        }
    }

    /**
     * Get params for widget
     * @param array $params
     * @return array
     */
    public function interfaceParams($params = array())
    {
        $jbvars = $this->app->jbvars;

        return array(
            'min'      => $jbvars->number($params->get('min', 1)),
            'max'      => $jbvars->number($params->get('max', 999999)),
            'step'     => $jbvars->number($params->get('step', 1)),
            'default'  => $jbvars->number($params->get('default', 1)),
            'decimals' => $jbvars->number($params->get('decimals', 0))
        );
    }

    /**
     * Load elements css/js assets
     * @return $this
     */
    public function loadAssets()
    {
        parent::loadAssets();

        // for cache mode
        $this->less('jbassets:less/widget/quantity.less');
        $this->js(array(
            'jbassets:js/widget/quantity.js',
            'cart-elements:price/quantity/assets/js/quantity.js'
        ));

        return $this;
    }
}
