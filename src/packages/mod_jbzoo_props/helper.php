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

require_once JPATH_ADMINISTRATOR . '/components/com_zoo/config.php';
require_once JPATH_BASE . '/media/zoo/applications/jbuniversal/framework/jbzoo.php';
require_once JPATH_BASE . '/media/zoo/applications/jbuniversal/framework/classes/jbmodulehelper.php'; // TODO move to bootstrap

/**
 * Class JBModuleHelperProps
 */
class JBModuleHelperProps extends JBModuleHelper
{
    /**
     * @param JRegistry $params
     * @param stdClass  $module
     */
    public function __construct(JRegistry $params, $module)
    {
        parent::__construct($params, $module);

        $this->app->jbfilter->set($this->getType(), $this->getAppId());
    }

    /**
     * Load styles
     */
    protected function _loadAssets()
    {
        parent::_loadAssets();
        $this->_jbassets->less('mod_jbzoo_props:assets/less/props.less');
    }

}
