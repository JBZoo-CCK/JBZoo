<?php
/**
 * JBZoo App is universal Joomla CCK, application for YooTheme Zoo component
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
require_once JPATH_BASE . '/media/zoo/applications/jbuniversal/framework/classes/jbmodulehelper.php'; // TODO move to bootstrap
require_once JPATH_BASE . '/modules/mod_jbzoo_item/types/jbzooitemtype.php';

/**
 * Class JBModuleHelperItem
 */
class JBModuleHelperItem extends JBModuleHelper
{
    const TYPE_PREFIX = 'JBZooModItem';

    /**
     * @var null
     */
    protected $_itemType = null;

    /**
     * @param JRegistry $params
     * @param stdClass  $module
     */
    public function __construct(JRegistry $params, $module)
    {
        parent::__construct($params, $module);

        $this->_loadType($params);
    }

    /**
     * Load module assets
     */
    protected function _loadAssets()
    {
        parent::_loadAssets();

        if ($this->_isRemoveViewed()) {
            $this->_jbassets->js('mod_jbzoo_item:assets/js/viewed.js');
        }
    }

    /**
     * Init remove viewed button
     */
    protected function _initWidget()
    {
        if ($this->_isRemoveViewed()) {
            $this->_jbassets->widget('#' . $this->getModuleId(), 'JBZoo.Viewed', array(
                'message'   => JText::_('JBZOO_MODITEM_RECENTLY_VIEWED_DELETE_HISTORY'),
                'url_clear' => $this->app->jbrouter->removeViewed()
            ));
        }

        if ((int)$this->_params->get('column_heightfix', 0)) {
            $this->_jbassets->widget('#' . $this->getModuleId(), 'JBZoo.HeightFix', array());
        }
    }

    /**
     * @param $params
     */
    protected function _loadType($params)
    {
        $fileType = $params->get('mode', 'category');

        $pathType = $this->app->path->path('mod_jbzoo_item:types/' . $fileType . '.php');

        $moduleType = JPath::clean($pathType);
        $className  = self::TYPE_PREFIX . ucfirst($fileType);

        if (JFile::exists($moduleType)) {
            require_once $moduleType;
        }

        if (class_exists($className)) {
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

    /**
     * @return string
     */
    public function renderRemoveButton()
    {
        if ($this->_isRemoveViewed()) {

            $attrs = array('class' => array(
                'recently-viewed-clear',
                'jsRecentlyViewedClear',
                'jbbutton',
                'small',
            ));

            return '<span ' . $this->attrs($attrs) . '>' . JText::_('JBZOO_MODITEM_DELETE') . '</span>';
        }

        return null;
    }

    /**
     * @return bool
     */
    protected function _isRemoveViewed()
    {
        return $this->_params->get('delete', 1) && $this->_params->get('mode') == 'viewed';
    }

}