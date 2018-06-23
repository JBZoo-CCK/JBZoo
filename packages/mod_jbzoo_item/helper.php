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
     * @type array|null
     */
    protected $_items = null;

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
            $this->_jbassets->js('jbassets:js/widget/heightfix.js');
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
        if (is_null($this->_items)) {
            $this->_items = $this->_itemType->getItems();
        }

        return $this->_items;
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

    /**
     * @param null  $layout
     * @param array $vars
     * @return string
     */
    public function partial($layout = null, $vars = array())
    {
        $vars['items'] = $this->getItems();
        return parent::partial($layout, $vars);
    }

}
