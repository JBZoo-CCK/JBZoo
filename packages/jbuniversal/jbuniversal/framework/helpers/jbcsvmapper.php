<?php
/**
 * JBZoo App is universal Joomla CCK, application for YooTheme Zoo component
 *
 * @package     jbzoo
 * @version     2.x Pro
 * @author      JBZoo App http://jbzoo.com
 * @copyright   Copyright (C) JBZoo.com,  All rights reserved.
 * @license     http://jbzoo.com/license-pro.php JBZoo Licence
 * @coder       Denis Smetannikov <denis@jbzoo.com>
 */

// no direct access
defined('_JEXEC') or die('Restricted access');


/**
 * Class JBCSVMapperHelper
 */
class JBCSVMapperHelper extends AppHelper
{
    const FIELD_CONTINUE = '__NO_EXPORT_DATA__';

    /**
     * @var JBCSVCellHelper
     */
    protected $_csvcell = null;

    /**
     * @param App $app
     */
    public function __construct($app)
    {
        parent::__construct($app);

        $this->_csvcell = $this->app->jbcsvcell;
    }

    /**
     * Get form item basic values
     * @param Item $item
     * @return array
     */
    public function getItemBasic(Item $item)
    {
        return array(
            'id'    => $this->_csvcell->createItem('id', $item, 'core')->toCSV(),
            'sku'   => $this->_csvcell->createItem('sku', $item, 'core')->toCSV(),
            'name'  => $this->_csvcell->createItem('name', $item, 'core')->toCSV(),
            'alias' => $this->_csvcell->createItem('alias', $item, 'core')->toCSV(),
        );
    }

    /**
     * Get form item core values
     * @param Item $item
     * @return array
     */
    public function getItemCore(Item $item)
    {
        return array(
            'author'   => $this->_csvcell->createItem('author', $item, 'core')->toCSV(),
            'created'  => $this->_csvcell->createItem('created', $item, 'core')->toCSV(),
            'category' => $this->_csvcell->createItem('category', $item, 'core')->toCSV(),
            'tags'     => $this->_csvcell->createItem('tags', $item, 'core')->toCSV(),
        );
    }

    /**
     * Get form item general config
     * @param Item $item
     * @return array
     */
    public function getItemConfig(Item $item)
    {
        $result = array(
            'state'                   => $this->_csvcell->createItem('state', $item, 'config')->toCSV(),
            'priority'                => $this->_csvcell->createItem('priority', $item, 'config')->toCSV(),
            'access'                  => $this->_csvcell->createItem('access', $item, 'config')->toCSV(),
            'searchable'              => $this->_csvcell->createItem('searchable', $item, 'config')->toCSV(),
            'publish_up'              => $this->_csvcell->createItem('publish_up', $item, 'config')->toCSV(),
            'publish_down'            => $this->_csvcell->createItem('publish_down', $item, 'config')->toCSV(),
            'comments'                => $this->_csvcell->createItem('comments', $item, 'config')->toCSV(),
            'frontpage'               => $this->_csvcell->createItem('frontpage', $item, 'config')->toCSV(),
            'category_primary'        => $this->_csvcell->createItem('category_primary', $item, 'config')->toCSV(),
            'teaser_image_align'      => $this->_csvcell->createItem('teaser_image_align', $item, 'config')->toCSV(),
            'full_image_align'        => $this->_csvcell->createItem('full_image_align', $item, 'config')->toCSV(),
            'related_image_align'     => $this->_csvcell->createItem('related_image_align', $item, 'config')->toCSV(),
            'subcategory_image_align' => $this->_csvcell->createItem('subcategory_image_align', $item, 'config')->toCSV(),
        );

        return $result;
    }

    /**
     * Get from item meta data
     * @param Item $item
     * @return array
     */
    public function getItemMeta(Item $item)
    {
        $result = array(
            'hits'                 => $this->_csvcell->createItem('hits', $item, 'meta')->toCSV(),
            'metadata_title'       => $this->_csvcell->createItem('title', $item, 'meta')->toCSV(),
            'metadata_description' => $this->_csvcell->createItem('description', $item, 'meta')->toCSV(),
            'metadata_keywords'    => $this->_csvcell->createItem('keywords', $item, 'meta')->toCSV(),
            'metadata_robots'      => $this->_csvcell->createItem('robots', $item, 'meta')->toCSV(),
            'metadata_author'      => $this->_csvcell->createItem('author', $item, 'meta')->toCSV(),
        );

        return $result;
    }

    /**
     * Get from item price data
     * @param Item $item
     * @return array
     */
    public function getItemPrice(Item $item)
    {
        $itemPrices = $item->getElementsByType('jbpriceadvance');
        $result     = array();
        $cell       = $this->_csvcell;

        if (!empty($itemPrices)) {
            $i = 0;
            foreach ($itemPrices as $identifier => $itemPrice) {
                $i++;

                $options = array(
                    'elementId' => $identifier
                );

                $result['price_sku_' . $i]               = $cell->createItem('price_sku', $item, 'price', $options)->toCSV();
                $result['price_balance_' . $i]           = $cell->createItem('price_balance', $item, 'price', $options)->toCSV();
                $result['price_basic_' . $i]             = $cell->createItem('price_basic', $item, 'price', $options)->toCSV();
                $result['price_currency_' . $i]          = $cell->createItem('price_currency', $item, 'price', $options)->toCSV();
                $result['price_description_' . $i]       = $cell->createItem('price_description', $item, 'price', $options)->toCSV();
                $result['price_discount_' . $i]          = $cell->createItem('price_discount', $item, 'price', $options)->toCSV();
                $result['price_discount_currency_' . $i] = $cell->createItem('price_discount_currency', $item, 'price', $options)->toCSV();
                $result['price_new_' . $i]               = $cell->createItem('price_new', $item, 'price', $options)->toCSV();
                $result['price_hit_' . $i]               = $cell->createItem('price_hit', $item, 'price', $options)->toCSV();
            }
        }

        return $result;
    }

    /**
     * Get from item user data
     * @param Item $item
     * @return array
     */
    public function getItemUser(Item $item)
    {
        $result = array();
        $type   = $item->getType();
        $params = $this->app->jbuser->getParam('export-items', array());

        $i = 0;
        foreach ($type->getElements() as $identifier => $element) {

            $elemValue = $this->_csvcell->createItem($element, $item, 'user')->toCSV();

            if ($elemValue != JBCSVMapperHelper::FIELD_CONTINUE) {
                if (!(int)$params->fields_full_price && $element->getElementType() == 'jbpriceadvance') {
                    continue;
                }
                $name          = $element->config->get('name') ? $element->config->get('name') : $element->getElementType();
                $name          = $name . ' (#' . ++$i . ')';
                $result[$name] = (array)$elemValue;
            }
        }

        return $result;
    }

    /**
     * Get data from category
     * @param Category $category
     * @return array
     */
    public function getCategory(Category $category)
    {
        return array(
            'id'                      => $this->_csvcell->createCategory('id', $category, 'category')->toCSV(),
            'name'                    => $this->_csvcell->createCategory('name', $category, 'category')->toCSV(),
            'alias'                   => $this->_csvcell->createCategory('alias', $category, 'category')->toCSV(),
            'description'             => $this->_csvcell->createCategory('description', $category, 'category')->toCSV(),
            'parent'                  => $this->_csvcell->createCategory('parent', $category, 'category')->toCSV(),
            'ordering'                => $this->_csvcell->createCategory('ordering', $category, 'category')->toCSV(),
            'published'               => $this->_csvcell->createCategory('published', $category, 'category')->toCSV(),
            'title'                   => $this->_csvcell->createCategory('title', $category, 'category')->toCSV(),
            'subtitle'                => $this->_csvcell->createCategory('subtitle', $category, 'category')->toCSV(),
            'image'                   => $this->_csvcell->createCategory('image', $category, 'category')->toCSV(),
            'teaser_text'             => $this->_csvcell->createCategory('teaser_text', $category, 'category')->toCSV(),
            'teaser_image'            => $this->_csvcell->createCategory('teaser_image', $category, 'category')->toCSV(),
            'metadata_title'          => $this->_csvcell->createCategory('metadata_title', $category, 'category')->toCSV(),
            'metadata_description'    => $this->_csvcell->createCategory('metadata_description', $category, 'category')->toCSV(),
            'metadata_keywords'       => $this->_csvcell->createCategory('metadata_keywords', $category, 'category')->toCSV(),
            'metadata_robots'         => $this->_csvcell->createCategory('metadata_robots', $category, 'category')->toCSV(),
            'metadata_author'         => $this->_csvcell->createCategory('metadata_author', $category, 'category')->toCSV(),
            'items_per_page'          => $this->_csvcell->createCategory('items_per_page', $category, 'category')->toCSV(),
            'subcategory_items_count' => $this->_csvcell->createCategory('subcategory_items_count', $category, 'category')->toCSV(),
            'tmpl_category'           => $this->_csvcell->createCategory('tmpl_category', $category, 'category')->toCSV(),
            'tmpl_subcategory'        => $this->_csvcell->createCategory('tmpl_subcategory', $category, 'category')->toCSV(),
            'tmpl_item'               => $this->_csvcell->createCategory('tmpl_item', $category, 'category')->toCSV(),
            'config_category'         => $this->_csvcell->createCategory('config_category', $category, 'category')->toCSV(),
            'config_items'            => $this->_csvcell->createCategory('config_items', $category, 'category')->toCSV(),
            'config_layouts'          => $this->_csvcell->createCategory('config_layouts', $category, 'category')->toCSV(),
            'config_others'           => $this->_csvcell->createCategory('config_others', $category, 'category')->toCSV(),
            'config_items_order'      => $this->_csvcell->createCategory('config_items_order', $category, 'category')->toCSV(),
        );
    }


    /**
     * Get categories filds for import
     * @return array
     */
    public function getCategoryFields()
    {
        return array(
            'core'     => array(
                'id'        => JText::_('JBZOO_CATEGORY_ID'),
                'name'      => JText::_('JBZOO_CATEGORY_NAME'),
                'alias'     => JText::_('JBZOO_CATEGORY_ALIAS'),
                'parent'    => JText::_('JBZOO_CATEGORY_PARENT'),
                'ordering'  => JText::_('JBZOO_CATEGORY_ORDERING'),
                'published' => JText::_('JBZOO_CATEGORY_PUBLISHED'),
            ),
            'content'  => array(
                'title'          => JText::_('JBZOO_CATEGORY_TITLE'),
                'description'    => JText::_('JBZOO_CATEGORY_DESCRIPTION'),
                'subtitle'       => JText::_('JBZOO_CATEGORY_SUBTITLE'),
                'image'          => JText::_('JBZOO_CATEGORY_IMAGE'),
                'teaser_text'    => JText::_('JBZOO_CATEGORY_TEASER_TEXT'),
                'teaser_image'   => JText::_('JBZOO_CATEGORY_TEASER_IMAGE'),
            ),
            'meta'     => array(
                'metadata_title'       => JText::_('JBZOO_CATEGORY_METADATA_TITLE'),
                'metadata_description' => JText::_('JBZOO_CATEGORY_METADATA_DESCRIPTION'),
                'metadata_keywords'    => JText::_('JBZOO_CATEGORY_METADATA_KEYWORDS'),
                'metadata_robots'      => JText::_('JBZOO_CATEGORY_METADATA_ROBOTS'),
                'metadata_author'      => JText::_('JBZOO_CATEGORY_METADATA_AUTHOR'),
            ),
            'template' => array(
                'tmpl_category'           => JText::_('JBZOO_CATEGORY_TMPL_CATEGORY'),
                'tmpl_subcategory'        => JText::_('JBZOO_CATEGORY_TMPL_SUBCATEGORY'),
                'tmpl_item'               => JText::_('JBZOO_CATEGORY_TMPL_ITEM'),
                'config_category'         => JText::_('JBZOO_CATEGORY_CONFIG_CATEGORY'),
                'config_items'            => JText::_('JBZOO_CATEGORY_CONFIG_ITEMS'),
                'config_layouts'          => JText::_('JBZOO_CATEGORY_CONFIG_LAYOUTS'),
                'config_others'           => JText::_('JBZOO_CATEGORY_CONFIG_OTHERS'),
                'config_items_order'      => JText::_('JBZOO_CATEGORY_CONFIG_ITEMS_ORDER')
            )
        );
    }


    /**
     * Fields for item import
     * @param array $elementTypes
     * @return array
     */
    public function getItemFields($elementTypes)
    {
        $result = array(
            'basic'  => array(
                'id'    => JText::_('JBZOO_ITEM_ID'),
                //'sku'   => JText::_('JBZOO_ITEM_SKU'), // TODO replace to price.price_id value
                'name'  => JText::_('JBZOO_ITEM_NAME'),
                'alias' => JText::_('JBZOO_ITEM_ALIAS'),
            ),
            'core'   => array(
                'author'   => JText::_('JBZOO_ITEM_AUTHOR'),
                'created'  => JText::_('JBZOO_ITEM_CREATED'),
                'category' => JText::_('JBZOO_ITEM_CATEGORY'),
                'tags'     => JText::_('JBZOO_ITEM_TAGS'),
            ),
            'config' => array(
                'state'                   => JText::_('JBZOO_ITEM_STATE'),
                'priority'                => JText::_('JBZOO_ITEM_PRIORITY'),
                'access'                  => JText::_('JBZOO_ITEM_ACCESS'),
                'searchable'              => JText::_('JBZOO_ITEM_SEARCHABLE'),
                'publish_up'              => JText::_('JBZOO_ITEM_PUBLISH_UP'),
                'publish_down'            => JText::_('JBZOO_ITEM_PUBLISH_DOWN'),
                'comments'                => JText::_('JBZOO_ITEM_COMMENTS'),
                'frontpage'               => JText::_('JBZOO_ITEM_FRONTPAGE'),
                'category_primary'        => JText::_('JBZOO_ITEM_CATEGORY_PRIMARY'),
                'teaser_image_align'      => JText::_('JBZOO_ITEM_CONFIG_TEASER_IMAGE_ALIGN'),
                'full_image_align'        => JText::_('JBZOO_ITEM_CONFIG_FULL_IMAGE_ALIGN'),
                'related_image_align'     => JText::_('JBZOO_ITEM_CONFIG_RELATED_IMAGE_ALIGN'),
                'subcategory_image_align' => JText::_('JBZOO_ITEM_CONFIG_SUBCATEGORY_ALIGN')
            ),
            'meta'   => array(
                'hits'                 => JText::_('JBZOO_ITEM_METADATA_HITS'),
                'metadata_title'       => JText::_('JBZOO_ITEM_METADATA_TITLE'),
                'metadata_description' => JText::_('JBZOO_ITEM_METADATA_DESCRIPTION'),
                'metadata_keywords'    => JText::_('JBZOO_ITEM_METADATA_KEYWORDS'),
                'metadata_robots'      => JText::_('JBZOO_ITEM_METADATA_ROBOTS'),
                'metadata_author'      => JText::_('JBZOO_ITEM_METADATA_AUTHOR'),
            ),
            'user'   => array(),
        );

        foreach ($elementTypes as $elements) {

            foreach ($elements as $element) {

                $elementType = strtolower($element->getElementType());

                if ($elementType == 'jbpriceadvance') {

                    $postFix = '__' . $element->identifier;
                    $name    = $element->config->get('name');

                    $result['price__' . $name] = array(
                        'price_sku' . $postFix               => JText::_('JBZOO_ITEM_PRICE_SKU'),
                        'price_balance' . $postFix           => JText::_('JBZOO_ITEM_PRICE_BALANCE'),
                        'price_basic' . $postFix             => JText::_('JBZOO_ITEM_PRICE_BASIC'),
                        'price_currency' . $postFix          => JText::_('JBZOO_ITEM_PRICE_CURRENCY'),
                        'price_description' . $postFix       => JText::_('JBZOO_ITEM_PRICE_DESCRIPTION'),
                        'price_discount' . $postFix          => JText::_('JBZOO_ITEM_PRICE_DISCOUNT'),
                        'price_discount_currency' . $postFix => JText::_('JBZOO_ITEM_PRICE_DISCOUNT_CURRENCY'),
                        'price_new' . $postFix               => JText::_('JBZOO_ITEM_PRICE_NEW'),
                        'price_hit' . $postFix               => JText::_('JBZOO_ITEM_PRICE_HIT'),
                    );
                }

                $result['user'][$elementType . '__' . $element->identifier] =
                    $element->config->get('name') . ' (' . ucfirst($elementType) . ')';
            }
        }

        return $result;
    }

    /**
     * @param $fieldName
     * @return mixed
     */
    public function itemFieldToMeta($fieldName)
    {
        $fieldName = strtolower(trim($fieldName));

        $assign = array(
            // core group
            'id'                      => array('group' => 'core', 'name' => 'id'),
            'sku'                     => array('group' => 'core', 'name' => 'sku'),
            'name'                    => array('group' => 'core', 'name' => 'name'),
            'tags'                    => array('group' => 'core', 'name' => 'tags'),
            'alias'                   => array('group' => 'core', 'name' => 'alias'),
            'author'                  => array('group' => 'core', 'name' => 'author'),
            'created'                 => array('group' => 'core', 'name' => 'created'),
            'category'                => array('group' => 'core', 'name' => 'category'),
            // config group
            'state'                   => array('group' => 'config', 'name' => 'state'),
            'access'                  => array('group' => 'config', 'name' => 'access'),
            'comments'                => array('group' => 'config', 'name' => 'comments'),
            'priority'                => array('group' => 'config', 'name' => 'priority'),
            'frontpage'               => array('group' => 'config', 'name' => 'frontpage'),
            'searchable'              => array('group' => 'config', 'name' => 'searchable'),
            'publish_up'              => array('group' => 'config', 'name' => 'publish_up'),
            'publish_down'            => array('group' => 'config', 'name' => 'publish_down'),
            'category_primary'        => array('group' => 'config', 'name' => 'category_primary'),
            'teaser_image_align'      => array('group' => 'config', 'name' => 'teaser_image_align'),
            'full_image_align'        => array('group' => 'config', 'name' => 'full_image_align'),
            'related_image_align'     => array('group' => 'config', 'name' => 'related_image_align'),
            'subcategory_image_align' => array('group' => 'config', 'name' => 'subcategory_image_align'),
            // meta group
            'hits'                    => array('group' => 'meta', 'name' => 'hits'),
            'metadata_title'          => array('group' => 'meta', 'name' => 'title'),
            'metadata_robots'         => array('group' => 'meta', 'name' => 'robots'),
            'metadata_author'         => array('group' => 'meta', 'name' => 'author'),
            'metadata_keywords'       => array('group' => 'meta', 'name' => 'keywords'),
            'metadata_description'    => array('group' => 'meta', 'name' => 'description'),
            //price group
            'price_sku'               => array('group' => 'price', 'name' => 'price_sku'),
            'price_balance'           => array('group' => 'price', 'name' => 'price_balance'),
            'price_basic'             => array('group' => 'price', 'name' => 'price_basic'),
            'price_currency'          => array('group' => 'price', 'name' => 'price_currency'),
            'price_description'       => array('group' => 'price', 'name' => 'price_description'),
            'price_discount'          => array('group' => 'price', 'name' => 'price_discount'),
            'price_discount_currency' => array('group' => 'price', 'name' => 'price_discount_currency'),
            'price_new'               => array('group' => 'price', 'name' => 'price_new'),
            'price_hit'               => array('group' => 'price', 'name' => 'price_hit'),
        );

        if (isset($assign[$fieldName])) {
            return $assign[$fieldName];
        }

        // user group
        list($name, $elementId) = explode('__', $fieldName);

        if (isset($assign[$name])) {
            $assign[$name]['elementId'] = $elementId;
            return $assign[$name];
        }

        return array('group' => 'user', 'name' => $name, 'elementId' => $elementId);
    }

    /**
     * @param $fieldName
     * @return mixed
     */
    public function categoryFieldToMeta($fieldName)
    {
        $fieldName = strtolower(trim($fieldName));
        $assign    = array(
            'id'                      => array('group' => 'category', 'name' => 'id'),
            'name'                    => array('group' => 'category', 'name' => 'name'),
            'alias'                   => array('group' => 'category', 'name' => 'alias'),
            'parent'                  => array('group' => 'category', 'name' => 'parent'),
            'ordering'                => array('group' => 'category', 'name' => 'ordering'),
            'published'               => array('group' => 'category', 'name' => 'published'),
            'title'                   => array('group' => 'category', 'name' => 'title'),
            'description'             => array('group' => 'category', 'name' => 'description'),
            'subtitle'                => array('group' => 'category', 'name' => 'subtitle'),
            'image'                   => array('group' => 'category', 'name' => 'image'),
            'teaser_text'             => array('group' => 'category', 'name' => 'teaser_text'),
            'teaser_image'            => array('group' => 'category', 'name' => 'teaser_image'),
            'metadata_title'          => array('group' => 'category', 'name' => 'metadata_title'),
            'metadata_description'    => array('group' => 'category', 'name' => 'metadata_description'),
            'metadata_keywords'       => array('group' => 'category', 'name' => 'metadata_keywords'),
            'metadata_robots'         => array('group' => 'category', 'name' => 'metadata_robots'),
            'metadata_author'         => array('group' => 'category', 'name' => 'metadata_author'),
            'items_per_page'          => array('group' => 'category', 'name' => 'items_per_page'),
            'subcategory_items_count' => array('group' => 'category', 'name' => 'subcategory_items_count'),
            'tmpl_category'           => array('group' => 'category', 'name' => 'tmpl_category'),
            'tmpl_subcategory'        => array('group' => 'category', 'name' => 'tmpl_subcategory'),
            'tmpl_item'               => array('group' => 'category', 'name' => 'tmpl_item'),
            'config_category'         => array('group' => 'category', 'name' => 'config_category'),
            'config_items'            => array('group' => 'category', 'name' => 'config_items'),
            'config_layouts'          => array('group' => 'category', 'name' => 'config_layouts'),
            'config_others'           => array('group' => 'category', 'name' => 'config_category'),
            'config_items_order'      => array('group' => 'category', 'name' => 'config_items_order'),

        );


        if (isset($assign[$fieldName])) {
            return $assign[$fieldName];
        }

        return null;
    }

}