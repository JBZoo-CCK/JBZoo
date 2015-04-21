<?php
/**
 * JBZoo App is universal Joomla CCK, application for YooTheme Zoo component
 *
 * @package     jbzoo
 * @version     2.x Pro
 * @author      JBZoo App http://jbzoo.com
 * @copyright   Copyright (C) JBZoo.com,  All rights reserved.
 * @license     http://jbzoo.com/license-pro.php JBZoo Licence
 * @coder       Vitaliy Yanovskiy <joejoker@jbzoo.com>
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

/**
 * Class JBCSVItemPrice
 */
class JBCSVItemPrice extends JBCSVItem
{
    /**
     * @type JBCartElementPrice
     */
    protected $_param;

    /**
     * Constructor
     * @param JBCartElementPrice $element
     * @param ElementJBPrice     $jbPrice
     * @param array              $options
     */
    public function __construct($element, $jbPrice, $options = array())
    {
        parent::__construct($jbPrice, $jbPrice->getItem(), $options);

        $this->_param = $element;
    }

    /**
     * @return mixed|JBCartValue
     */
    public function toCSV()
    {
        return $this->_param->get('value', null);
    }

    /**
     * @param           $value
     * @param  int|null $variant
     * @return Item|void
     */
    public function fromCSV($value, $variant = 0)
    {
        return array('value' => $value);
    }
}