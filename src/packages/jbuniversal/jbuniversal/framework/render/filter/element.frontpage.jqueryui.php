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
 * Class JBFilterElementFrontpageJqueryUI
 */
class JBFilterElementFrontpageJqueryUI extends JBFilterElementFrontpage
{
    /**
     * Render HTML
     * @return mixed
     */
    function html()
    {
        $options = $this->_getValues('bool');

        return $this->app->jbhtml->buttonsJQueryUI(
            $this->_createOptionsList($options),
            $this->_getName(),
            $this->_attrs,
            $this->_value,
            $this->_getId()
        );
    }

}
