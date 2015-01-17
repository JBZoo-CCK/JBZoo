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
 * Class JBExportHelper
 */
class JBExportHelper extends AppHelper
{

    const STEP_SIZE = 500;

    /**
     * @var JBCSVMapperHelper
     */
    protected $_mapper = null;

    /**
     * @param App $app
     */
    public function __construct($app)
    {
        parent::__construct($app);

        $this->app->jbenv->maxPerformance();
        $this->_mapper = $this->app->jbcsvmapper;
    }

    /**
     * Categories to CSV
     * @param null $appId
     * @param array $options
     * @return bool|string
     * @throws AppException
     */
    public function categoriesToCSV($appId = null, $options = array())
    {
        $files  = array();
        $offset = 0;

        $options['limit'] = array($offset, self::STEP_SIZE);

        while ($categories = $this->_getCategoryList($appId, $options)) {

            $categoriesByApp = $this->app->jbarray->groupByKey($categories, 'application_id');

            foreach ($categoriesByApp as $appId => $categories) {
                $application   = $this->app->table->application->get($appId);
                $files[$appId] = $this->_exportCategoryToFile($categories, $application->alias, $options);
            }

            // shift the offset
            $offset += self::STEP_SIZE;
            $options['limit'] = array($offset, self::STEP_SIZE);
        }

        return $files;
    }

    /**
     * Items to CSV
     * @param $appId
     * @param $typeId
     * @param null $catId
     * @param array $options
     * @return bool|string
     * @throws AppException
     */
    public function itemsToCSV($appId = null, $catId = null, $typeId = null, $options = array())
    {
        $files  = array();
        $offset = 0;

        $options['limit'] = array($offset, self::STEP_SIZE);

        while ($items = $this->_getItemList($appId, $catId, $typeId, $options)) {

            $itemsByTypes = $this->app->jbarray->groupByKey($items, 'type');

            // clean memory
            $idList = array_keys($items);
            foreach ($idList as $id) {
                $this->app->table->item->unsetObject($id);
            }
            unset($items, $idList);

            // convert items group to csv
            foreach ($itemsByTypes as $itemType => $items) {
                $files[$itemType] = $this->_exportTypeToFile($items, $itemType, $options);
            }

            // shift the offset
            $offset += self::STEP_SIZE;
            $options['limit'] = array($offset, self::STEP_SIZE);
        }

        return $files;
    }

    /**
     * Export data to CSV file by item type
     * @param array $items
     * @param string $typeId
     * @param array $options
     * @return bool|string
     */
    protected function _exportTypeToFile(array $items, $typeId, $options)
    {
        $maxima = $data = array();
        foreach ($items as $item) {
            $data[$item->id] = $this->_mapper->getItemBasic($item);

            if ((int)$options->get('fields_core')) {
                $data[$item->id] = array_merge($data[$item->id], $this->_mapper->getItemCore($item));
            }

            if ((int)$options->get('fields_user')) {
                $data[$item->id] = array_merge($data[$item->id], $this->_mapper->getItemUser($item));
            }

            if ((int)$options->get('fields_price')) {
                $data[$item->id] = array_merge($data[$item->id], $this->_mapper->getItemPrice($item));
            }

            if ((int)$options->get('fields_config')) {
                $data[$item->id] = array_merge($data[$item->id], $this->_mapper->getItemConfig($item));
            }

            if ((int)$options->get('fields_meta')) {
                $data[$item->id] = array_merge($data[$item->id], $this->_mapper->getItemMeta($item));
            }

            // check multiple fields
            foreach ($data[$item->id] as $key => $value) {
                if (is_array($value)) {
                    $maxima[$key] = max(1, @$maxima[$key], count($value));
                }
            }
        }

        return $this->app->jbcsv->toFile($data, 'items_' . $typeId, $maxima);
    }

    /**
     * Export data to CSV file by item type
     * @param array $categories
     * @param int $appId
     * @param array $options
     * @return bool|string
     */
    protected function _exportCategoryToFile(array $categories, $appId, $options = null)
    {
        $data = array();
        foreach ($categories as $category) {
            $data[$category->id] = $this->_mapper->getCategory($category);
        }

        return $this->app->jbcsv->toFile($data, 'categories_' . $appId);
    }

    /**
     * Get item list
     * @param $appId
     * @param $typeId
     * @param null $catId
     * @param array $options
     * @return mixed
     */
    protected function _getItemList($appId = null, $catId = null, $typeId = null, $options = array())
    {
        if (!isset($options['order'])) {
            $options['order'] = 'id';
        }

        return JBModelItem::model()->getList($appId, $catId, $typeId, $options);
    }

    /**
     * Get category list
     * @param $appId
     * @param array $options
     * @return mixed
     */
    protected function _getCategoryList($appId = null, $options = array())
    {
        return JBModelCategory::model()->getList($appId, $options);
    }

    /**
     * Remove old temp files
     */
    public function clean()
    {
        $folder = $this->app->jbpath->sysPath('tmp', "/jbzoo-export/");

        if (JFolder::exists($folder)) {
            JFolder::delete($folder);
        }

        JFolder::create($folder);
    }

}
