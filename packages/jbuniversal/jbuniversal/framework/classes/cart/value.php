<?php


class JBCartValue
{
    /**
     * @var float
     */
    protected $_value = 0.0;

    /**
     * @var string
     */
    protected $_currency = '';

    /**
     * @var JBMoneyHelper
     */
    protected $_jbmoney = null;

    /**
     * @param mixed $data
     * @param null $currecny
     */
    public function __construct($data, $currecny = null)
    {
        $this->_money = $this->app->jbmoney;
        //$this->_currency = JBCart::getInstance()->getDefaultStatus();
    }

    public function add(JBCartValue $value)
    {
        $newValue = $this->_money->calc($this->_value, $this->_currency, $value);
        return $this;
    }


    public function multiple($count)
    {
        $newValue = $this->_money->calc($this->_value, $value);
        return $this;
    }

    public function addModify(JBCartElementModifyPrice $element)
    {
        $element->modyfy($this);
    }
}