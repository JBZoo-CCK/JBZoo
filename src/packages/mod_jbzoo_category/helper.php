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
 * Class JBModuleHelperCategory
 */
class JBModuleHelperCategory extends JBModuleHelper
{
    /**
     * @param JRegistry $params
     * @param stdClass  $module
     */
    public function __construct(JRegistry $params, $module)
    {
        parent::__construct($params, $module);

        $this->_initParams();
    }

    /**
     * Load module assets
     */
    protected function _loadAssets()
    {
        parent::_loadAssets();

        $this->_jbassets->less('mod_jbzoo_category:assets/styles.less');
    }

    /**
     * @return array|null
     */
    public function getCategories()
    {
        $renderCat  = array();
        $appId      = (int)$this->_params->get('application', false);
        $menuItem   = (int)$this->_params->get('menu_item', 0);
        $categories = $this->_getCategories();
        $curCatId   = $this->getCurrentCategory();

        if ($appId && !empty($categories)) {

            foreach ($categories as $category) {

                if ($menuItem) {
                    $catUrl = $this->app->route->category($category, true, $menuItem);
                } else {
                    $catUrl = $this->app->route->category($category);
                }

                $currentCat = array(
                    'active_class'  => ($curCatId == $category->id) ? 'category-active' : '',
                    'cat_link'      => $catUrl,
                    'category_name' => $category->name,
                    'item_count'    => null,
                    'desc'          => null,
                    'image'         => null,
                    'items'         => array(),
                );

                if ((int)$this->_params->get('display_count_items', 1)) {
                    $currentCat['item_count'] = $this->app->table->item->getItemCountFromCategory($category->application_id, $category->id, true);
                }

                if ((int)$this->_params->get('category_display_image', 1) && $image = $category->getImage('content.category_teaser_image')) {
                    $currentCat['image'] = $this->_getImageSettings($image);
                    $currentCat['attr']  = $this->_getImageSettings($image, true);
                }

                if ((int)$this->_params->get('display_items', 1)) {
                    $currentCat['items'] = $this->_getItems($category->id);
                }

                if ((int)$this->_params->get('category_display_description', false)) {
                    $currentCat['desc'] = $category->getText($category->params->get('content.category_teaser_text'));
                }

                $renderCat[$category->id] = $currentCat;
            }
        }

        return $renderCat;
    }

    /**
     * @param      $image
     * @param bool $attr
     * @return string
     */
    protected function _getImageSettings($image, $attr = false)
    {
        $imgAttrs = array(
            'src'    => $image['src'],
            'width'  => $image['width'],
            'height' => $image['height'],
        );

        if ((int)$this->_params->get('category_image_width') || (int)$this->_params->get('category_image_height')) {

            $width  = (int)$this->_params->get('category_image_width', 100);
            $height = (int)$this->_params->get('category_image_height', 100);
            $image  = $this->app->jbimage->resize($image['path'], $width, $height);

            $imgAttrs = array_merge($imgAttrs, array(
                'src'    => $image->url,
                'width'  => $image->width,
                'height' => $image->height,
            ));
        }

        return ($attr) ? $imgAttrs : '<img ' . $this->app->jbhtml->buildAttrs($imgAttrs) . ' />';
    }

    /**
     * Get category list
     * @return array
     */
    protected function _getCategories()
    {
        $categories = JBModelCategory::model()->getList(
            $this->_params->get('app_id'),
            array(
                'limit'     => $this->_params->get('category_limit'),
                'parent'    => $this->_params->get('cat_id'),
                'order'     => $this->_params->get('category_order'),
                'published' => 1,
            )
        );
        return $categories;
    }

    /**
     * Get items
     * @param $catId
     * @return mixed
     */
    protected function _getItems($catId)
    {
        $items = JBModelItem::model()->getList(
            $this->_params->get('app_id'),
            $catId,
            $this->_params->get('type_id', false),
            array(
                'limit'     => $this->_params->get('items_limit'),
                'published' => 1,
                'order'     => $this->_params->get('item_order'),
            )
        );

        return $items;
    }

    /**
     * Set mixed params for module
     */
    protected function _initParams()
    {
        list($appId, $catId) = explode(':', $this->_params->get('application', '0:0'));
        $itemsLimit    = (int)$this->_params->get('items_limit', 4);
        $categoryLimit = (int)$this->_params->get('category_limit', 0);

        ($itemsLimit == 0) ? $this->_params->set('items_limit', null) : $this->_params->set('items_limit', $itemsLimit);
        ($categoryLimit == 0) ? $this->_params->set('category_limit', null) : $this->_params->set('category_limit', $categoryLimit);

        $this->_params->set('app_id', (int)$appId);
        $this->_params->set('cat_id', (int)$catId);

    }

    /**
     * @return int
     */
    public function getCurrentCategory()
    {
        return $this->app->jbrequest->getSystem('category', 0);
    }
}