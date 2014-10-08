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
 * Class JBCartElementPriceImage
 */
class JBCartElementPriceImage extends JBCartElementPrice
{
    /**
     * @return mixed|null|string
     */
    public function edit()
    {
        $params = $this->getParams();

        if ($layout = $this->getLayout('edit.php')) {
            return self::renderLayout($layout, array(
                'params' => $params
            ));
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
        $unique = $this->unique($params);

        if ($layout = $this->getLayout()) {
            return self::renderLayout($layout, array(
                'params'  => $params,
                'element' => $unique
            ));
        }

        return NULL;
    }

    /**
     * @param $params
     *
     * @return string
     */
    public function unique($params)
    {
        $image = json_decode($params->get('image'));

        return $params->get('_price_layout') . '_' . $this->getJBPrice()->getItem()->id . '_' . $image->element;
    }

}
