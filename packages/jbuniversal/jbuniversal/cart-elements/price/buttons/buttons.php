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
 * Class JBCartElementPriceButtons
 */
class JBCartElementPriceButtons extends JBCartElementPrice
{
    /**
     * @param array $params
     *
     * @return bool
     */
    public function hasFilterValue($params = array())
    {
        return false;
    }

    /**
     * @param  array $params
     *
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
        $params   = $this->app->data->create($params);
        $template = $params->get('template', 'add');

        if ($layout = $this->getLayout($template . '.php')) {
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
        $params  = $this->getRenderParams();
        $jbPrice = $this->_jbprice;
        $item    = $jbPrice->getItem();

        return array(
            'add'    => $this->app->jbrouter->element($jbPrice->identifier, $item->id, 'ajaxAddToCart',
                array(
                    'template' => $jbPrice->getTemplate()
                )),
            'remove' => $this->app->jbrouter->element($jbPrice->identifier, $item->id, 'ajaxRemoveFromCart'),
            'modal'  => $this->app->jbrouter->element($jbPrice->identifier, $item->id, 'ajaxModalWindow',
                array(
                    'elem_layout'   => $params->get('_layout'),
                    'elem_position' => $params->get('_position'),
                    'elem_index'    => $params->get('_index'),
                )),
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
}
