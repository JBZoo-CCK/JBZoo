<?php
/**
 * JBZoo App is universal Joomla CCK, application for YooTheme Zoo component
 *
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
 * Class JBCartElementCurrency
 */
abstract class JBCartElementStatus extends JBCartElement
{
    protected $_namespace = JBCart::ELEMENT_TYPE_STATUS;

    /**
     * @return string
     */
    public function getCode()
    {
        $code = $this->config->get('code', $this->identifier);
        if (!$code) {
            $code = $this->identifier;
        }

        return $code;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->getCode();
    }

}

/**
 * Class JBCartElementStatusException
 */
class JBCartElementStatusException extends JBCartElementException
{
}
