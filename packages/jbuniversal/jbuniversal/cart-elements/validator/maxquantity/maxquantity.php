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
 * Class JBCartElementValidatorMaxQuantity
 */
class JBCartElementValidatorMaxQuantity extends JBCartElementValidator
{
    /**
     * @return mixed|void
     * @throws JBCartElementValidatorException
     */
    public function isValid()
    {
        $items = $this->_order->getItems();
        $value = $this->_getValue();

        foreach ($items as $item) {

            if ($value > 0 && $item->get('quantity') > $value) {
                $message = JText::sprintf('JBZOO_ELEMENT_VALIDATOR_MAXQUANTITY_ERROR', $value);
                throw new JBCartElementValidatorException($message);
            }

        }
    }

    /**
     * @param array $params
     * @return string
     */
    public function render($params = array())
    {
        $message = JText::sprintf('JBZOO_ELEMENT_VALIDATOR_MAXQUANTITY_MESSAGE', $this->_getValue());
        return $message;
    }

    /**
     * @return JBCartValue
     */
    protected function _getValue()
    {
        $value = $this->app->jbvars->number($this->config->get('value'));
        return $value;
    }

}
