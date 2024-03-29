<?php
use Joomla\String\StringHelper;
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
 * Class JBCartElementModifierOrderPriceDiscountCode
 */
class JBCartElementModifierOrderPriceDiscountCode extends JBCartElementModifierOrderPrice
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
    }

    /**
     * @return JBCartValue
     */
    public function getRate()
    {   
        if ($this->session->get('JBZooRemoveAllModifiers', false)) {
            return $this->_order->val(0);
        }
        
        if ($this->_isInList()) {
            $rate = $this->_order->val($this->config->get('rate'));
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
     * @param $code
     */
    public function ajaxSetCode($code = null)
    {
        $this->bindData(array('code' => $code));
        $inList = $this->_isInList();

        $result = array(
            'cart' => JBCart::getInstance()->recount()
        );

        if (!$inList) {
            $result['message'] = JText::_('JBZOO_ELEMENT_MODIFIERORDERPRICE_DISCOUNTCODE_NOTFOUND');
            $this->bindData(array('code' => ''));
        }

        $this->app->jbajax->send($result, $inList);
    }

    /**
     * @return bool
     */
    protected function _isInList()
    {
        $list = $this->config->get('codelist');
        $list = $this->app->jbstring->parseLines($list);

        $code = StringHelper::trim($this->get('code'));

        return in_array($code, $list);
    }

}
