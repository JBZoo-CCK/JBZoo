<?php
declare(strict_types=1);

use Joomla\Filesystem\Path;
use Joomla\Registry\Registry;

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
    protected ?array $_items = null;

    /**
     * @param Registry $params
     * @param object $module
     */
    public function __construct(Registry $params, object $module)
    {
        parent::__construct($params, $module);

        $this->_loadType($params);
    }

    /**
     * Load module assets
     * @return void
     */
    protected function _loadAssets(): void
    {
        parent::_loadAssets();

        if ($this->_isRemoveViewed()) {
            $this->_jbassets->js(['mod_jbzoo_item:assets/js/viewed.js']);
        }
    }

    /**
     * Init remove viewed button
     * @return void
     */
    protected function _initWidget(): void
    {
        if ($this->_isRemoveViewed()) {
            $this->_jbassets->widget('#' . $this->getModuleId(), 'JBZoo.Viewed', [
                'message' => JText::_('JBZOO_MODITEM_RECENTLY_VIEWED_DELETE_HISTORY'),
                'url_clear' => $this->app->jbrouter->removeViewed()
            ]);
        }

        if ((int)$this->_params->get('column_heightfix', 0)) {
            $this->_jbassets->js(['jbassets:js/widget/heightfix.js']);
            $this->_jbassets->widget('#' . $this->getModuleId(), 'JBZoo.HeightFix');
        }
    }

    /**
     * @param $params
     * @return void
     */
    protected function _loadType($params): void
    {
        $fileType = $params->get('mode', 'category');

        $pathType = $this->app->path->path('mod_jbzoo_item:types/' . $fileType . '.php');

        $moduleType = Path::clean($pathType);
        $className = self::TYPE_PREFIX . ucfirst($fileType);

        if (is_file($moduleType)) {
            require_once $moduleType;
        }

        if (class_exists($className)) {
            $this->_itemType = new $className($params);
        }
    }

    /**
     * @return array|null
     */
    public function getItems(): ?array
    {
        if (is_null($this->_items)) {
            $this->_items = $this->_itemType->getItems();
        }

        return $this->_items;
    }

    /**
     * @return string|null
     */
    public function renderRemoveButton(): ?string
    {
        if ($this->_isRemoveViewed()) {

            $attrs = [
                'class' => [
                    'recently-viewed-clear',
                    'jsRecentlyViewedClear',
                    'jbbutton',
                    'small',
                ]
            ];

            return '<span ' . $this->attrs($attrs) . '>' . JText::_('JBZOO_MODITEM_DELETE') . '</span>';
        }

        return null;
    }

    /**
     * @return bool
     */
    protected function _isRemoveViewed(): bool
    {
        return $this->_params->get('delete', 1) && $this->_params->get('mode') == 'viewed';
    }

    /**
     * @param null $layout
     * @param array $vars
     * @return string|null
     */
    public function partial($layout = null, $vars = []): ?string
    {
        $vars['items'] = $this->getItems();
        return parent::partial($layout, $vars);
    }

}
