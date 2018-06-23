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