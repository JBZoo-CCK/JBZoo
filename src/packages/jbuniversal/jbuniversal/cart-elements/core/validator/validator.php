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
