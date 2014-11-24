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
 * Class JBCartElementValidatorMinsum
 */
class JBCartElementValidatorMinsum extends JBCartElementValidator
{
    /**
     * @return mixed|void
     * @throws JBCartElementValidatorException
     */
    public function isValid()
    {
        $summa = $this->_order->getTotalSum();
        $value = $this->_order->val($this->config->get('value'));

        if ($value->isPositive() && $value->compare($summa, '>')) {
            $message = JText::sprintf('JBZOO_ELEMENT_MINSUM_ERROR', $value->text());
            throw new JBCartElementValidatorException($message);
        }

    }
}
