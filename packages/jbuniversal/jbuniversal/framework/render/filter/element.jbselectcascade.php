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
 * Class JBFilterElementJBSelectcascade
 */
class JBFilterElementJBSelectcascade extends JBFilterElement
{
    /**
     * Render HTML
     * @return string|null
     */
    function html()
    {
        $selectInfo = $this->app->jbselectcascade->getItemList(
            $this->_config->get('select_names', ''),
            $this->_config->get('items', '')
        );

        return $this->app->jbhtml->selectCascade(
            $selectInfo,
            $this->_getName('%s'),
            $this->_value,
            $this->_attrs,
            $this->_getId()
        );
    }
}
