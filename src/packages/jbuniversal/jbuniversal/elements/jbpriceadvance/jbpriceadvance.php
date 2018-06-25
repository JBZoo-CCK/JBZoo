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
