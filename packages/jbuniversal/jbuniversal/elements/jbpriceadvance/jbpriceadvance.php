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
 * Class ElementJBPriceAdvance
 * The Price element for JBZoo
 */
class ElementJBPriceAdvance extends Element implements iSubmittable
{
    /**
     * @param array $params
     * @return bool
     */
    public function hasValue($params = array())
    {
        return true;
    }

    /**
     * @return null|string
     */
    public function edit()
    {
        return implode(PHP_EOL, array('<div><p>',
            'Ops...! JBPrice Advance is depricated now!',
            'So, please use new JBprice Plain instead old version.',
            'Or, you can use converter 2.1.5 to 2.2.x in tools (JBZoo tab).',
            '</p></div>'
        ));
    }

    /**
     * Render submission
     * @param array $params
     * @return null|string
     */
    public function renderSubmission($params = array())
    {
        return $this->edit($params);
    }

    /**
     * Render for front-end
     * @param array $params
     * @return string|void
     */
    public function render($params = array())
    {
        return 'Deprecated element! Please, use new element JBPrice Plain';
    }

    /**
     * Validate submission
     * @param $value
     * @param $params
     * @return mixed
     * @throws AppValidatorException
     */
    public function validateSubmission($value, $params)
    {
        return parent::validateSubmission($value, $params);
    }

    public function getElementConfig()
    {
        return null;
    }
    
}
