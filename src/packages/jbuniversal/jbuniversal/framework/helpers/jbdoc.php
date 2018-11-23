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
 * Class JBDocHelper
 */
class JBDocHelper extends AppHelper
{

    /**
     * Set meta for noindex and nofollow
     */
    public function noindex()
    {
        $doc = JFactory::getDocument();

        // set meta
        $doc->setMetadata('robots', 'noindex, nofollow');
    }

    /**
     * Disable Joomla template
     */
    public function disableTmpl()
    {
        $this->app->jbrequest->set('tmpl', 'component');
    }

    /**
     * Disable Joomla template
     */
    public function rawOutput()
    {
        $this->app->jbrequest->set('tmpl', 'raw');
    }

}
