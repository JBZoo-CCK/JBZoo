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
 * Class JBCartElementModifierPrice
 */
abstract class JBCartElementModifierPrice extends JBCartElement
{
    protected $_namespace = JBCart::ELEMENT_TYPE_MODIFIERPRICE;

    /**
     * @param JBCartValue $summa
     * @param \Item       $item
     * @return \JBCartValue
     */
    abstract public function modify(JBCartValue $summa, $item = null);

    /**
     * @return JBCartValue
     */
    public function getRate()
    {
        return $this->_order->val(0);
    }

    public function getOrderData()
    {
        return $this->app->data->create(array(
            'rate'   => $this->data(),
            'config' => (array)$this->config,
        ));
    }

}

/**
 * Class JBCartElementModifierItemException
 */
class JBCartElementModifierPriceException extends JBCartElementException
{
}
