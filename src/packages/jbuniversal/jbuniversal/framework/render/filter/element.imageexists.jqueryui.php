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
 * Class JBFilterElementImageexistsJqueryui
 */
class JBFilterElementImageexistsJqueryui extends JBFilterElement
{
    /**
     * Return html
     * @return null|string
     */
    public function html()
    {
        $options = array(
            array(
                'text'  => JText::_('JBZOO_YES'),
                'value' => JBModelElementJBImage::IMAGE_EXISTS,
                'count' => null
            ),
            array(
                'text'  => JText::_('JBZOO_NO'),
                'value' => JBModelElementJBImage::IMAGE_NO_EXISTS,
                'count' => null
            )
        );

        return $this->app->jbhtml->buttonsJQueryUI(
            $this->_createOptionsList($options),
            $this->_getName(),
            $this->_attrs,
            $this->_value,
            $this->_getId()
        );
    }

}
