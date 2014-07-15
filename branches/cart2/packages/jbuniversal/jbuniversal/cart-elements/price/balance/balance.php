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
    /**
     * @param  array $params
     * @return mixed|null|string
     */
    public function edit($params = array())
    {
        $params = $this->getParams();

        if ((int)$params->get('basic', 0)) {
            return $this->getBasicTmpl();
        }

        if ($layout = $this->getLayout('default.php')) {

            return self::renderLayout($layout, array(
                'params' => $params
            ));
        }

        return null;
    }

    public function render($params = array())
    {

    }
}
