<?php
/**
 * JBZoo App is universal Joomla CCK, application for YooTheme Zoo component
 *
 * @package     jbzoo
 * @version     2.x Pro
 * @author      JBZoo App http://jbzoo.com
 * @copyright   Copyright (C) JBZoo.com,  All rights reserved.
 * @license     http://jbzoo.com/license-pro.php JBZoo Licence
 * @coder       Alexander Oganov <t_tapak@yahoo.com>
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

require_once dirname(__FILE__) . '/relateditems.php';

/**
 * Class JBCSVItemUserRelatedItemsPro
 * It's a hack for import RelatedItemsPro element.
 * Use at your own risk.
 */
class JBCSVItemUserRelatedItemsPro extends JBCSVItemUserRelatedItems
{
    /**
     * @var DatabaseHelper
     */
    public $db;

    /**
     * Class constructor
     *
     * @param Element|String $element
     * @param Item $item
     * @param array $options
     */
    public function __construct($element, Item $item = null, $options = array())
    {
        parent::__construct($element, $item, $options);

        $this->db     = JFactory::getDbo();
        $this->_value = $this->_getRelatedItems();

        if($listener = $this->_getListener()) {
            $this->app->event->dispatcher->disconnect('item:saved', array($listener, 'biRelate'));
        }
    }

    /**
     * @param $value
     * @param null $position
     * @return Item|void
     */
    public function fromCSV($value, $position = null)
    {
        $itemsAlias = $this->_getArray($value, JBCSVItem::SEP_ROWS, 'alias');
        $result     = array();

        foreach ($itemsAlias as $alias) {
            if ($item = JBModelItem::model()->getByAlias($alias, $this->_item->application_id)) {
                $result[] = $item->id;
                $this->_addRelation($item->id);
            }
        }

        $result = array_unique($result);

        $this->_element->bindData(array('item' => $result));
    }

    /**
     * Get related items pro
     * @return array
     */
    protected function _getRelatedItems()
    {
        $ids   = array();
        $query = $this->db->getQuery(true);

        $query->select('ritem_id, params')
            ->from('#__zoo_relateditemsproxref')
            ->where('item_id = ' . $this->_item->id)
            ->where('element_id='.$this->db->Quote($this->_element->identifier))
            ->order('id ASC');

        $itemsData = $this->db->setQuery($query)->loadAssocList();

        foreach($itemsData as $value) {
            $ids['item'][] = $value['ritem_id'];
        }

        return $ids;
    }

    /**
     * @param  null | int $ritem
     * @return bool
     */
    protected function _addRelation($ritem = null)
    {
        $query = $this->db->getQuery(true);

        // get items
        $query->select('id, params')
            ->from('#__zoo_relateditemsproxref')
            ->where('item_id = '  . (int) $ritem)
            ->where('ritem_id ='  . (int) $this->_item->id)
            ->where('element_id=' . $this->db->Quote($this->_identifier));

        $existing = $this->db->setQuery($query)->loadAssoc();

        $params = $this->_item->params->get('relateditemspro.', array());
        $params = json_encode($params);

        // If params are not the same
        if(isset($existing) && array_key_exists('params', $existing) && @$existing['params'] != $params){

            // remove relation so we can reinsert it with the right params
            $query->clear()
                ->delete('#__zoo_relateditemsproxref')
                ->where('item_id = '  . (int) $ritem)
                ->where('ritem_id ='  . (int) $this->_item->id)
                ->where('element_id=' . $this->db->Quote($this->_identifier));

            $this->db->setQuery($query)->execute();

        }

        // If the relation wasn't existing or the params weren't the same
        if( !$existing || @$existing['params'] != $params) {

            // add relation if entry does not exist
            $query->insert('#__zoo_relateditemsproxref')
                ->set('item_id = '   . (int) $ritem)
                ->set('ritem_id = '  . (int) $this->_item->id)
                ->set('element_id= ' . $this->db->Quote($this->_identifier))
                ->set('params = '    . $this->db->Quote($params));

            $this->db->setQuery($query)->execute();

        }

        return true;
    }

    /**
     * Get ElementRelatedItemsPro Object
     * @return bool|ElementRelatedItemsPro
     */
    protected function _getListener()
    {
        $listeners = $this->app->event->dispatcher->getListeners('item:saved');

        foreach($listeners as $listener) {
            if ($listener['0'] instanceof ElementRelatedItemsPro) {
                return $listener['0'];
            }
        }

        return false;
    }
}