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
 * Class JBFilterElementHidden
 */
class JBFilterElementCategoryHidden extends JBFilterElementCategory
{

    /**
     * Render HTML code for element
     * @return string|null
     */
    public function html()
    {
        $value = (int)$this->_params->get('jbzoo_filter_default', 0);
        if (!$value) {
            $value = $this->app->jbrequest->getSystem('category', '');
        }

        $this->_isMultiple = false;
        return $this->app->jbhtml->hidden(
            $this->_getName(),
            $value,
            $this->_attrs,
            $this->_getId()
        );
    }
}
