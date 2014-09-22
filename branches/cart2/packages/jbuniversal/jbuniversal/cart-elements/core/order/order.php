<?php
/**
 * JBZoo App is universal Joomla CCK, application for YooTheme Zoo component
 *
 * @package     jbzoo
 * @version     2.x Pro
 * @author      JBZoo App http://jbzoo.com
 * @copyright   Copyright (C) JBZoo.com,  All rights reserved.
 * @license     http://jbzoo.com/license-pro.php JBZoo Licence
 * @coder       Denis Smetannikov <denis@jbzoo.com>
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

/**
 * Class JBCartElementOrder
 */
abstract class JBCartElementOrder extends JBCartElement
{
    protected $_namespace = JBCart::ELEMENT_TYPE_ORDER;

    /**
     * Render shipping in order
     *
     * @param  array
     * @return bool|string
     */
    public function edit($params = array())
    {
        $value = $this->get('value');

        if ($layout = $this->getLayout('edit.php')) {
            return self::renderLayout($layout, array(
                'params' => $params,
                'value'  => $value,
            ));
        }

        return $value;
    }

}

/**
 * Class JBCartElementOrderException
 */
class JBCartElementOrderException extends JBCartElementException
{
}
