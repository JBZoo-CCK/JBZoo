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
 * Class JBCartElementPriceBalance
 */
class JBCartElementPriceBalance extends JBCartElementPrice
{
    const NOT_AVAILABLE = 0;
    const AVAILABLE     = -1;
    const UNDER_ORDER   = -2;

    /**
     * @param  array $params
     * @return mixed|null|string
     */
    public function edit($params = array())
    {
        $params = $this->getParams();

        if ($layout = $this->getLayout('edit.php')) {

            return self::renderLayout($layout, array(
                'params' => $params
            ));
        }

        return null;
    }

    /**
     * @param array $params
     * @return array|mixed|null|string
     */
    public function render($params = array())
    {
        $params   = $this->app->data->create($params);
        $template = $params->get('template', 'simple');

        if ($layout = $this->getLayout($template . '.php')) {
            return self::renderLayout($layout, array(
                'textNo'    => '<span class="not-available">' . JText::_('JBZOO_JBPRICE_NOT_AVAILABLE') . '</span>',
                'textYes'   => '<span class="available">' . JText::_('JBZOO_JBPRICE_AVAILABLE') . '</span>',
                'textOrder' => '<span class="under-order">' . JText::_('JBZOO_JBPRICE_BALANCE_UNDER_ORDER') . '</span>'
            ));
        }

        return null;
    }
}
