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
 */

// no direct access
defined('_JEXEC') or die('Restricted access');
use Joomla\String\StringHelper;
/**
 * Class JBCartElementEmailSiteName
 */
class JBCartElementEmailSiteName extends JBCartElementEmail
{

    /**
     * @param  array $params
     * @return bool
     */
    public function hasValue($params = array())
    {
        return $this->_getSitename();
    }

    /**
     * @return string
     */
    protected function _getSitename()
    {
        $config   = $config = JFactory::getConfig();
        $sitename = StringHelper::trim($config->get('sitename'));
        return $sitename;
    }

}
