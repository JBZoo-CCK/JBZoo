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
 * Class JBModelElementItemfrontpage
 */
class JBModelElementItemfrontpage extends JBModelElement
{

    /**
     * Set AND element conditions
     * @param JBDatabaseQuery $select
     * @param string $elementId
     * @param string|array $value
     * @param int $i
     * @param bool $exact
     * @return JBDatabaseQuery
     */
    public function conditionAND(JBDatabaseQuery $select, $elementId, $value, $i = 0, $exact = false)
    {
        return $this->_getWhere($value);
    }

    /**
     * Set OR element conditions
     * @param JBDatabaseQuery $select
     * @param string $elementId
     * @param string|array $value
     * @param int $i
     * @param bool $exact
     * @return array
     */
    public function conditionOR(JBDatabaseQuery $select, $elementId, $value, $i = 0, $exact = false)
    {
        return $this->_getWhere($value);
    }

    /**
     * Get where conditions
     * @param $value
     * @return string
     */
    /**
     * Get where conditions
     * @param  $value
     * @return array
     */
    protected function _getWhere($value)
    {
        $value = is_array($value) ? $value[key($value)] : $value;

        $rows = $this->_getItemIdsByCategoryIds((int)$value);

        if (!empty($rows)) {
            return array('tItem.id IN (' . implode(',', $rows) . ')');
        }

        return array();
    }

    /**
     * Get ItemId's by categoriesId's
     * @param  boolean $frontpage
     * @return array|JObject
     */
    protected function _getItemIdsByCategoryIds($frontpage = true)
    {
        $result = array();

            $select = $this->_getSelect()
                ->select('tCategoryItem.item_id')
                ->from(ZOO_TABLE_CATEGORY_ITEM . ' AS tCategoryItem')
                ->innerJoin(ZOO_TABLE_ITEM . ' AS tItem ON tItem.id = tCategoryItem.item_id')
                ->where('tCategoryItem.category_id = 0')
                ->where('tItem.application_id = ?', $this->_applicationId);

        if($frontpage) {
            $result = $this->fetchList($select);
        }

        if(!$frontpage) {
            $select = $this->_getSelect()
                ->clear()
                ->select('tItem.id')
                ->from(ZOO_TABLE_ITEM . ' AS tItem')
                ->where('tItem.' . $this->app->user->getDBAccessString())
                ->where('tItem.state = ?', 1)
                ->where('tItem.type = ?', $this->_itemType)
                ->where('tItem.id NOT IN(' . $select . ')');

            $result = $this->fetchList($select);
        }

        return $result;
    }

}
