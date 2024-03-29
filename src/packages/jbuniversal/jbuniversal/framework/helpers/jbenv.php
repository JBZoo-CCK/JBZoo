<?php
use \Joomla\CMS\Factory;

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
 * Class JBEnvHelper
 */
class JBEnvHelper extends AppHelper
{

    /**
     * Is current application is front-end
     * @return bool
     */
    public function isSite()
    {
        if (PHP_SAPI == 'cli') {
            return true;
        }

        return \Joomla\CMS\Factory::getApplication()->isClient('site');
    }


    /**
     * Get current template name
     * @return string
     */
    public function getTemplateName()
    {
        $templateName = 'catalog';
        $currentApp   = $this->app->zoo->getApplication();
        if ($currentApp && $currentApp->getTemplate()) {
            $templateName = $currentApp->getTemplate()->name;
        }

        return $templateName;
    }

    /**
     * Get full current URL with simple optimization
     */
    public function getCurrentUrl()
    {
        static $url;

        if (!isset($url)) {
            $url = JUri::getInstance()->toString();
        }

        return $url;
    }

    /**
     * Check, is widgetkit enabled
     */
    public function isWidgetkit($isFree = true)
    {
        $isFreeResult = JFile::exists(JPATH_ADMINISTRATOR . '/components/com_widgetkit/classes/widgetkit.php')
            && JComponentHelper::getComponent('com_widgetkit', true)->enabled
            && JFile::exists(JPATH_ADMINISTRATOR . '/components/com_zoo/config.php')
            && JComponentHelper::getComponent('com_zoo', true)->enabled;

        if ($isFreeResult && $isFree) {
            return true;
        }

        if ($isFreeResult && !$isFree && $this->app->path->path('media:widgetkit/widgets/accordion')) {
            return true;
        }

        return false;
    }

    /**
     * Set max pefomance mode to script
     */
    public function maxPerformance()
    {
        // set max time
        @ini_set('max_execution_time', 1800);
        if (function_exists('set_time_limit')) {
            @set_time_limit(1800);
        }

        // set memory limit
        @ini_set('memory_limit', '512M');
    }

}
