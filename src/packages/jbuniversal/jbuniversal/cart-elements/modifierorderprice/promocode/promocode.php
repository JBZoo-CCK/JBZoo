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
 * Class JBCartElementModifierOrderPricePromoCode
 */
class JBCartElementModifierOrderPricePromoCode extends JBCartElementModifierOrderPrice
{
    /**
     * @param App    $app
     * @param string $type
     * @param string $group
     */
    public function __construct($app, $type, $group)
    {
        parent::__construct($app, $type, $group);
        $this->registerCallback('ajaxSetCode');

        // Get config
        $this->_config = JBModelConfig::model();

        // Get session
        $this->session = JFactory::getSession();
    }

    /**
     * @return JBCartValue
     */
    public function getRate()
    {   
        $discount = $this->getDiscount();
        $isActive = $this->isActive($discount);

        // if (!$isActive['result']) {
        //     JFactory::getApplication()->enqueueMessage($isActive['msg'], 'error');
        // }

        if ($discount && $isActive['result']) {
            $rate = $this->_order->val($discount['value']);
            $rate->negative();
            return $rate;
        }

        return $this->_order->val(0);
    }

    /**
     * @param array $params
     * @return string|void
     */
    public function renderSubmission($params = array())
    {
        if ($layout = $this->getLayout('submission.php')) {
            return self::renderLayout($layout, array(
                'params' => $params,
            ));
        }

        return null;
    }

        /**
     * @return null|string
     */
    public function edit()
    {
        $rate = $this->_order->val($this->get('rate'));
        return $rate->html(null, true).($this->get('code') ? '('.$this->get('code').')' : '');
    }

    /**
     * @param $code
     */
    public function ajaxSetCode($code = null)
    {   
        $this->session->set('JBZooRemoveAllModifiers', false);

        $this->bindData(array('code' => $code));

        $discount           = $this->getDiscount();
        $isActive           = $this->isActive($discount);

        if ($isActive['result']) {
            $isOthersModifiers  = $discount['other'];

            if (!$isOthersModifiers) {
               $this->session->set('JBZooRemoveAllModifiers', true);
            }
        }

        $result = array(
            'cart' => JBCart::getInstance()->recount()
        );

        if (!$isActive['result']) {
            $result['message'] = $isActive['msg'];
            $this->bindData(array('code' => ''));
        }

        $this->app->jbajax->send($result, $isActive['result']);
    }

    /**
     * @return array
     */
    protected function getDiscount()
    {   
        $rate  = 0;
        $code  = JString::trim($this->get('code'));
        $list  = $this->_config->get(JBCart::DEFAULT_POSITION, array(), 'cart.' . JBCart::CONFIG_PROMO);

        if ($list) {
            foreach ($list as $key => $promoCode) {
                if ($promoCode['code'] == $code) {
                    return $promoCode;
                }
            }
        }

        return array();
    }

    /**
     * Set data through data array.
     * @param array $data
     * @return $this
     */
    public function bindData($data = array())
    {
        parent::bindData($data);

        // Connect to backet aftersave event
        $this->app->event->dispatcher->connect('basket:saved', array($this, 'afterbasketsave'));

        return $this;
    }

    /**
     * @return null
     */
    public function afterBasketSave()
    {
        $discount   = $this->getDiscount();
        $isActive   = $this->isActive($discount);

        if ($isActive['result']) {
            $code       = $discount['code'];
            $list       = $this->_config->get(JBCart::DEFAULT_POSITION, array(), 'cart.' . JBCart::CONFIG_PROMO);

            if ($list && is_array($list)) {
                foreach ($list as $key => $promoCode) {
                    if ($promoCode['code'] == $code) {
                        $count                  = $promoCode['count'];
                        $list[$key]['count']    = $count + 1;

                        $this->_config->set(JBCart::DEFAULT_POSITION, $list, 'cart.' . JBCart::CONFIG_PROMO, 'data');
                    }
                }
            }
        }
    }

    /**
     * @return bool
     */
    public function isActive($discount)
    {
        if (empty($discount) || empty($discount['code'] || empty($discount['value']))) {
            return array(
                'result' => false,
                'msg'    => JText::_('JBZOO_ELEMENT_MODIFIERORDERPRICE_PROMOCODE_VALIDATOR_EMPTY')
            );
        }

        // Enable Check 

        if (!$discount['enable']) {
            return array(
                'result' => false,
                'msg'    => JText::sprintf('JBZOO_ELEMENT_MODIFIERORDERPRICE_PROMOCODE_VALIDATOR_ENABLE', $discount['code'])
            );
        }

        // Sum check

        if (!empty($discount['min'])) {
            $summa = $this->getOrder()->getTotalForItems();
            $value = $this->getOrder()->val($discount['min']);

            if ($value->compare($summa, '>')) {
                return array(
                    'result' => false,
                    'msg'    => JText::sprintf('JBZOO_ELEMENT_MODIFIERORDERPRICE_PROMOCODE_VALIDATOR_MIN', $discount['code'], $value->text())
                );
            }
        }

        // Limit check

        if (!empty($discount['limit']) && (int) $discount['limit'] > 0) {
            if ($discount['count'] >= $discount['limit']) {
                return array(
                    'result' => false,
                    'msg'    => JText::sprintf('JBZOO_ELEMENT_MODIFIERORDERPRICE_PROMOCODE_VALIDATOR_LIMIT', $discount['code'])
                );
            }
        }

        // Date check

        if (!empty($discount['date'])) {
            $date   = strtotime($discount['date']);
            $now    = strtotime('now');

            if ($now >= $date) {
                return array(
                    'result' => false,
                    'msg'    => JText::sprintf('JBZOO_ELEMENT_MODIFIERORDERPRICE_PROMOCODE_VALIDATOR_DATE', $discount['code'])
                );
            }
        }

        return array(
            'result' => true,
            'msg'    => ''
        );
    }
}
