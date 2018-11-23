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
 * Class JBPaneHelper
 */
class JBPaneHelper extends AppHelper
{
    /**
     * @var bool
     */
    protected $_isJoomla3 = false;

    /**
     * @var int
     */
    protected $_tabIndex = 0;

    /**
     * @param App $app
     */
    public function __construct($app)
    {
        parent::__construct($app);

        $this->_isJoomla3 = !$this->app->jbversion->joomla('2.7.0'); // Joomla 2.5.x, 3.0.x, 3.1.x and latter
    }

    /**
     * Start tabs widget
     * @param string $key
     * @return mixed
     */
    public function startWidget($key = 'jbzoo-tabs')
    {
        if (!$this->_isJoomla3) {
            return JHtml::_('bootstrap.startTabSet', $key, array('active' => 'jbzoo-tab-' . $this->_tabIndex));
        } else {
            return JHtml::_('tabs.start', 'jbzoo_admin_tabs', array('useCookie' => 1));
        }
    }

    /**
     * End tab widget
     * @param string $key
     * @return mixed
     */
    public function endWidget($key = 'jbzoo-tabs')
    {
        if (!$this->_isJoomla3) {
            return JHtml::_('bootstrap.endTabSet');
        } else {
            return JHtml::_('tabs.end');
        }
    }

    /**
     * Start tab
     * @param $name
     * @param string $key
     * @return mixed
     */
    public function startTab($name, $key = 'jbzoo-tabs')
    {
        if (!$this->_isJoomla3) {
            return JHtml::_('bootstrap.addTab', $key, 'jbzoo-tab-' . ($this->_tabIndex++), JText::_($name, true));
        } else {
            return JHtml::_('tabs.panel', JText::_($name, true), 'jbzoo-tab-' . ($this->_tabIndex++));
        }
    }

    /**
     * End  widget
     * @param string $key
     * @return string
     */
    public function endTab($key = 'jbzoo-tabs')
    {
        if (!$this->_isJoomla3) {
            return JHtml::_('bootstrap.endTab');
        } else {
            return '';
        }
    }

}