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
 * Class JBCartElementModifierOrderPrice
 */
abstract class JBCartElementModifierOrderPrice extends JBCartElement
{
    protected $_namespace = JBCart::ELEMENT_TYPE_MODIFIER_ORDER_PRICE;

    /**
     * @param JBCartValue $summa
     * @return JBCartValue
     */
    public function modify(JBCartValue $summa)
    {
        $rate = $this->getRate();
        $summa->add($rate);

        return $summa;
    }

    /**
     * @return JBCartValue
     */
    abstract public function getRate();

    /**
     * @param array $params
     * @return string
     */
    public function renderSubmission($params = array())
    {
        return $this->getRate()->html();
    }

    /**
     * Validates the submitted element
     * @param $value
     * @param $params
     * @return array
     */
    public function validateSubmission($value, $params)
    {
        $this->bindData($value);
        $value->set('rate', $this->getRate()->data());

        return $value;
    }

    /**
     * @return null|string
     */
    public function edit()
    {
        $rate = $this->_order->val($this->get('rate'));
        return $rate->html(null, true);
    }

    /**
     * @param array $data
     * @return $this
     */
    public function bindData($data = array())
    {
        if (!$this->getOrder()->id) {
            JBCart::getInstance()->setModifier($this->identifier, $data);
        }

        return parent::bindData($data);
    }

    /**
     * @return JSONData
     */
    public function getOrderData()
    {
        $this->set('rate', $this->getRate()->data());
        return $this->data();
    }

}

/**
 * Class JBCartElementModifierItemException
 */
class JBCartElementModifierPriceException extends JBCartElementException
{
}
