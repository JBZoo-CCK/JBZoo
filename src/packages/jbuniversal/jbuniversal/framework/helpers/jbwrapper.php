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
 * Class JBWrapperHelper
 */
class JBWrapperHelper extends AppHelper
{

    /**
     * Tags on start
     * @return string
     */
    public function start()
    {
        echo $this->app->zoo->getApplication()->jbtemplate->wrapStart();
    }

    /**
     * Tags on end
     * @return string
     */
    public function end()
    {
        echo $this->app->zoo->getApplication()->jbtemplate->wrapEnd();
    }

}
