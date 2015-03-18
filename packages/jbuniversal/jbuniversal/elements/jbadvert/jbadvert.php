<?php
/**
 * JBZoo App is universal Joomla CCK, application for YooTheme Zoo component
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
 * Class ElementJBAdvert
 */
class ElementJBAdvert extends Element implements iSubmittable
{
    /**
     * Array of element values
     * @type array
     */
    public $data = array();

    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();

        $this->data = array(
            '0' => JText::_('JBZOO_JBADVERT_VALUE_ACTIVATE')
        );
    }

    /**
     * Modify item
     */
    public function modify()
    {
        $data = $this->data();
        if (!empty($data)) {
            $item = $this->getItem();
            foreach ($data as $key => $value) {
                if (!$item->state && array_key_exists($value, $this->data)) {
                    $item->setState(1, true);
                }
            }
        }
    }

    /**
     * Check if elements value is set
     * @param array $params
     * @return bool
     */
    public function hasValue($params = array())
    {
        return false;
    }

    /**
     * @param array $params
     * @return null|string
     */
    public function edit($params = array())
    {
        if ($layout = $this->getLayout('edit.php')) {
            return self::renderLayout($layout, array(
                'params' => $params,
                'data'   => $this->addPrices($this->data)
            ));
        }

        return false;
    }

    /**
     * Renders the element
     * @param array $params Render parameters
     * @return string|void
     */
    public function render($params = array())
    {
        return false;
    }

    /**
     * Render submission
     * @param array $params
     * @return null|string
     */
    public function renderSubmission($params = array())
    {
        $tpl  = 'submission.php';
        $data = $this->data;

        if ($this->getItem()->state) {
            unset($data['0']);
        }

        if (empty($data)) {
            $tpl = 'no_services.php';
        }
        if ($layout = $this->getLayout($tpl)) {
            return self::renderLayout($layout, array(
                'params' => $params,
                'data'   => $this->addPrices($data)
            ));
        }

        return false;
    }

    /**
     * Validate submission
     * @param $values
     * @param $params
     * @return mixed
     * @throws AppValidatorException
     */
    public function validateSubmission($values, $params)
    {
        $options  = array('required' => $params->get('required'));
        $messages = array('required' => 'Please choose an option.');

        $values = $this->app->validator
            ->create('foreach', $this->app->validator->create('string', $options, $messages), $options, $messages)
            ->clean($values->get('value'));

        foreach ($values as $key => $value) {
            if (!array_key_exists($value, $this->data)) {
                unset($values[$key]);
            }
        }

        return $values;
    }

    /**
     * Add to cart
     * @return $this
     */
    public function addToCart()
    {
        $cart = JBCart::getInstance();
        $data = $this->getData();

        $cart->addItem($data);

        return $this;
    }

    /**
     * @param bool $refresh
     * @return array
     */
    public function getData($refresh = false)
    {
        static $data;

        if (!isset($data) || $refresh === true) {
            $data = array(
                'key'        => $this->getSessionKey(),
                'item_id'    => $this->getItem()->id,
                'item_name'  => $this->getItem()->name,
                'element_id' => $this->identifier,
                'total'      => $this->getTotal()->data(true),
                'quantity'   => 1,
                'values'     => $this->getValues(),
                'elements'   => array(
                    '_value' => $this->getTotal()->data(true)
                ),
                'params'     => array(
                    '_quantity' => array(
                        'min'      => 1,
                        'max'      => 1,
                        'step'     => 1,
                        'default'  => 1,
                        'decimals' => 0
                    )
                ),
                'variant'    => 0
            );

            $data = $this->app->data->create($data);
        }

        return $data;
    }

    /**
     * Get total sum
     * @return JBCartValue
     */
    public function getTotal()
    {
        $prices = $this->config->get('prices');
        $total  = JBCart::val();

        if (!empty($prices)) {
            foreach ($prices as $price) {
                $total->add($price);
            }
        }

        return $total;
    }

    /**
     * @return array
     */
    public function getValues()
    {
        $data   = (array)$this->data();
        $values = array();

        if (!empty($data)) {
            $ok = JText::_('JBZOO_YES');
            foreach ($this->data as $key => $value) {
                $values[$value] = $ok;
            }
        }

        return $values;
    }

    /**
     * Get session key
     * @return string
     */
    public function getSessionKey()
    {
        return md5($this->identifier . implode($this->getValues()));
    }

    /**
     * @return int
     * @internal param int $key Number of variant
     */
    public function getBalance()
    {
        return 1;
    }

    /**
     * Get parameter form object to render input form.
     * @return Object
     */
    public function getConfigForm()
    {
        return parent::getConfigForm()->addElementPath(dirname(__FILE__));
    }

    /**
     * @param  integer $quantity
     * @return bool
     */
    public function inStock($quantity)
    {
        $quantity = (int)$quantity;

        if ($quantity != $this->getBalance()) {
            return false;
        }

        return (bool)$quantity;
    }

    /**
     * @param int $key
     */
    public function setDefault($key)
    {
        $this->set('default_variant', $key);
    }

    /**
     * Get services with prices
     * @param array $data Array of services
     * @return array
     */
    public function addPrices($data)
    {
        $config = $this->config;
        if (!empty($data)) {
            foreach ($data as $key => $value) {
                $price = $config->find('prices.' . $key, 0);

                $data[$key] .= ' <small>(' . JBCart::val($price)->text() . ')</small>';

            }
        }

        return $data;
    }
}
