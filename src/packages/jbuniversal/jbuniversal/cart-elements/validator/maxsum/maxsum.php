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
