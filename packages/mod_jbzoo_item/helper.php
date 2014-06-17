<?php
/**
 * JBZoo App is universal Joomla CCK, application for YooTheme Zoo component
 *
 * @package     jbzoo
 * @version     2.x Pro
 * @author      JBZoo App http://jbzoo.com
 * @copyright   Copyright (C) JBZoo.com,  All rights reserved.
 * @license     http://jbzoo.com/license-pro.php JBZoo Licence
 * @coder       Vitaliy Yanovskiy <joejoker@jbzoo.com>
 * @coder       Oganov Alexander <t_tapakm@yahoo.com>
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

require_once JPATH_ADMINISTRATOR . '/components/com_zoo/config.php';
require_once JPATH_BASE . '/media/zoo/applications/jbuniversal/framework/jbzoo.php';
require_once JPATH_BASE . '/modules/mod_jbzoo_item/types/jbzooitemtype.php';

/**
 * Class JBZooItemHelper
 */
class JBZooItemHelper
{
    /**
     * @var null
     */
    protected $_itemType = null;

    /**
     * Prefix for class
     * @var string
     */
    protected $_prefixType = 'JBZooModItem';

    /**
     * @param $params
     */
    public function loadType($params)
    {
        $app        = App::getInstance('zoo');
        $fileType   = $params->get('mode', 'category');
        $pathType   = $app->path->path('mod_jbzoo_item:' . DS . 'types' . DS . $fileType . '.php');
        $moduleType = JPath::clean($pathType);
        $className  = $this->_prefixType . ucfirst($fileType);
        require_once $moduleType;

        if (JFile::exists($moduleType) && class_exists($className)) {
            $this->_itemType = new $className($params);
        }
    }

    /**
     * @return mixed
     */
    public function getItems()
    {
        return $this->_itemType->getItems();
    }

}