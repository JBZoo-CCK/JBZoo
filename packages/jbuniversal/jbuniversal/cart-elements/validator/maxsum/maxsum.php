<?php
/**
 * JBZoo App is universal Joomla CCK, application for YooTheme Zoo component
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
 * Class JBCartElementValidatorMaxsum
 */
class JBCartElementValidatorMaxsum extends JBCartElementValidator
{
    /**
     * @return mixed|void
     * @throws JBCartElementValidatorException
     */
    public function isValid()
    {
        $summa = $this->_order->getTotalSum();
        $value = $this->_getValue();

        if ($value->isPositive() && $value->compare($summa, '<')) {
            $message = JText::sprintf('JBZOO_ELEMENT_VALIDATOR_MAXSUM_ERROR', $value->html());
            throw new JBCartElementValidatorException($message);
        }

    }

    /**
     * @param array $params
     * @return string
     */
    public function render($params = array())
    {
        $message = JText::sprintf('JBZOO_ELEMENT_VALIDATOR_MAXSUM_MESSAGE', $this->_getValue()->html());
        return $message;
    }

    /**
     * @return JBCartValue
     */
    protected function _getValue()
    {
        $value = $this->_order->val($this->config->get('value'));
        return $value;
    }

}
