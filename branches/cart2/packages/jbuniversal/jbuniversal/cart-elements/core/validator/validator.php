<?php
/**
 * JBZoo App is universal Joomla CCK, application for YooTheme Zoo component
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
 * Class JBCartElementValidator
 */
abstract class JBCartElementValidator extends JBCartElement
{
    protected $_namespace = JBCart::ELEMENT_TYPE_VALIDATOR;

    /**
     * @return mixed
     */
    abstract public function isValid();

    /**
     * @param array $params
     * @return bool
     */
    public function hasValue($params = array())
    {
        return true;
    }

}

/**
 * Class JBCartElementValidatorException
 */
class JBCartElementValidatorException extends JBCartElementException
{

}
