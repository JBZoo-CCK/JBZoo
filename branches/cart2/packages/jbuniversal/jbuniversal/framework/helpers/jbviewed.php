<?php
/**
 * JBZoo App is universal Joomla CCK, application for YooTheme Zoo component
 * @package     jbzoo
 * @version     2.x Pro
 * @author      JBZoo App http://jbzoo.com
 * @copyright   Copyright (C) JBZoo.com,  All rights reserved.
 * @license     http://jbzoo.com/license-pro.php JBZoo Licence
 * @coder       Alexander Oganov <t_tapak@yahoo.com>
 */

defined('_JEXEC') or die('Restricted access');

/**
 * Class JBViewedHelper
 * Responsible for the last viewed
 */
class JBViewedHelper extends AppHelper
{
    const ORDER_DESC   = 'desc';
    const ORDER_ASC    = 'asc';
    const ORDER_RANDOM = 'random';

    const SESSION_GROUP = 'viewed';

    const ITEMS_LIMIT = 50;

    /**
     * JBSessionHelper
     * @var null | Object
     */
    protected $_jbsession = null;

    /**
     * Class constructor
     * @param App $app
     */
    public function __construct($app)
    {
        parent::__construct($app);

        $this->_jbsession = $this->app->jbsession;
    }

    /**
     * Add params item into session - Item id, type, app_id
     * @param Item $item
     * @return boolean
     */
    public function add(Item $item)
    {
        $viewed = $this->getItems();

        if (!isset($viewed[$item->id]) &&
            count($viewed) >= self::ITEMS_LIMIT
        ) {
            return false;
        }

        if (isset($viewed[$item->id])) {
            unset($viewed[$item->id]);
        } else {
            $newGroup[$item->id] = array(
                'type'  => $item->type,
                'appId' => $item->application_id,
            );
        }

        $newGroup = $this->app->jbarray->unshiftAssoc((array)$viewed, $item->id, array(
            'type'  => $item->type,
            'appId' => $item->application_id,
        ));

        $this->setItems($newGroup);

        return true;
    }

    /**
     * Making request in model
     * @param  array $types
     * @param  array $ordered
     * @param  int   $limit
     * @return array
     */
    public function getList($types = array(), $ordered = array(), $limit = 20)
    {
        $tmpItems = $this->getItems();
        $currItem = $this->app->jbrequest->getSystem('item');
        $items    = array();

        if (!is_array($ordered)) {
            $ordered = (array)$ordered;
        }

        if (is_array($tmpItems)) {

            if (array_key_exists($currItem, $tmpItems)) {
                unset($tmpItems[$currItem]);
            }

            $tmpItems = array_slice($tmpItems, 0, $limit, true);
            foreach ($tmpItems as $key => $tmpItem) {
                $itemsIds[] = $key;
                $appsIds[]  = $tmpItem['appId'];
            }
        }

        if (!empty($itemsIds)) {

            $options = array(
                'id'        => $itemsIds,
                'limit'     => $limit,
                'published' => 1,
            );

            $items = JBModelItem::model()->getList(array_unique($appsIds), -1, array_unique($types), $options);
            $items = $this->app->jbarray->sortByArray($items, array_keys($tmpItems));
        }

        if (!empty($ordered['order']) && !empty($items)) {

            if ($ordered['order'] == self::ORDER_DESC) {
                $items = array_reverse($items);

            } elseif ($ordered['order'] == self::ORDER_RANDOM) {
                shuffle($items);

            }

        }

        return $items;
    }

    /**
     * Clear session - recently viewed
     * @param  string $group
     * @return mixed
     */
    public function clear($group = self::SESSION_GROUP)
    {
        $result = $this->_jbsession->clearGroup($group);
        return $result;
    }

    /**
     * Set new data for recently viewed
     * @param         $data
     * @param  string $group
     * @return mixed
     */
    public function setItems($data, $group = self::SESSION_GROUP)
    {
        $result = $this->_jbsession->setGroup($data, $group);
        return $result;
    }

    /**
     * Get recently viewed data
     * @param  string $group
     * @return mixed
     */
    public function getItems($group = self::SESSION_GROUP)
    {
        $group = $this->_jbsession->getGroup($group);
        return $group;
    }

}