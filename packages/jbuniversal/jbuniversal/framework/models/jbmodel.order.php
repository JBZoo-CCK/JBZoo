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
 * Class JBModelOrder
 */
class JBModelOrder extends JBModel
{
    /**
     * Create and return self instance
     * @return JBModelOrder
     */
    public static function model()
    {
        return new self();
    }

    /**
     * Constructor
     */
    protected function __construct()
    {
        parent::__construct();
        $this->app->jbtables->checkOrder();
    }

    /**
     * Get order by itemid
     *
     * @param $orderId
     *
     * @return JBCartOrder
     */
    public function getById($orderId)
    {
        $select = $this->_getSelect()
            ->select('*')
            ->from(ZOO_TABLE_JBZOO_ORDER)
            ->where('id = ?', (int)$orderId)
            ->limit(1);

        $data  = $this->fetchRow($select, true);
        $order = JBcart::getInstance()->newOrder();

        $order->setData($data);

        return $order;
    }

    /**
     * @param JBCartOrder $order
     *
     * @return mixed
     */
    public function save(JBCartOrder $order)
    {
        $this->app->jbtables->checkOrder();
        $this->app->jbeventmanager->fireListeners();

        $currencies = $order->getCurrencyList();
        $params     = $order->getParams();
        $params->set(JBCart::CONFIG_CURRENCIES, $currencies);

        $data = array(
            'id'              => $order->id,
            'status'          => $order->getStatus()->getCode(),
            'status_payment'  => $order->getPaymentStatus(),
            'status_shipping' => $order->getShippingStatus(),
            'created'         => $order->created,
            'created_by'      => $order->created_by,
            'total'           => $order->getTotalSum(),
            'items'           => $order->getItems(false),
            'fields'          => $order->getFields(),
            'shipping'        => $order->getShipping()->data(),
            'shippingfields'  => $order->getShippingFields(),
            'payment'         => $order->getPayment()->data(),
            'modifiers'       => $order->getModifiersData(),
            'params'          => $params,
            'comment'         => $order->comment,
        );

        $this->app->event->dispatcher->notify($this->app->event->create($order, 'basket:beforesave', array()));

        if (!$data['id']) {

            $order->id = $this->_insert($data, ZOO_TABLE_JBZOO_ORDER);

            //TODO hardcoded
            $order->setItemsData((string)$data['items']);
            $this->app->event->dispatcher->notify($this->app->event->create($order, 'basket:saved', array()));

        } else {

            $data['modified'] = $this->app->jbdate->toMySql();
            $this->_update($data, ZOO_TABLE_JBZOO_ORDER);
            $this->app->event->dispatcher->notify($this->app->event->create($order, 'basket:updated', array()));

        }

        if ($order->id) {
            $this->app->event->dispatcher->notify($this->app->event->create($order, 'basket:aftersave', array()));
        }

        return $order->id;
    }

    /**
     * @param array $filter
     *
     * @return array
     */
    public function getList($filter = array())
    {
        $select = $this->_getSelectByCond($filter);

        // create objects and bind data
        $rows = $this->fetchAll($select, true);

        $result = array();
        foreach ($rows as $row) {
            $order = JBCart::getInstance()->newOrder();
            $order->setData($row);
            $result[$order->id] = $order;
        }

        return $result;
    }

    /**
     * @param string $group
     * @return array|JObject
     */
    public function getStatusList($group)
    {
        $field = 'status';
        if (JBCart::STATUS_ORDER == $group) {
            $field = 'status';

        } else if (JBCart::STATUS_ORDER == $group) {
            $field = 'status_payment';

        } else if (JBCart::STATUS_ORDER == $group) {
            $field = 'status_shipping';
        }

        $select = $this->_getSelect()
            ->select('tOrder.' . $field)
            ->from(ZOO_TABLE_JBZOO_ORDER, 'tOrder')
            ->group('tOrder.' . $field);

        $rows = $this->fetchAll($select);

        return $rows;
    }

    /**
     *
     */
    public function getUserList()
    {
        $select = $this->_getSelect()
            ->select(array(
                'tOrder.created_by AS created_by',
                'tUsers.id AS user_id',
                'tUsers.name AS user_name'
            ))
            ->leftJoin('#__users AS tUsers ON tUsers.id = tOrder.created_by')
            ->from(ZOO_TABLE_JBZOO_ORDER, 'tOrder')
            ->group('tOrder.created_by');

        $rows = $this->fetchAll($select);

        return $rows;
    }

    /**
     * @param array $filter
     *
     * @return int
     */
    public function getCount($filter = array())
    {
        $select = $this->_getSelectByCond($filter);

        $select
            ->clear('select')
            ->clear('limit')
            ->select('COUNT(tOrder.id) AS count');

        $result = $this->fetchRow($select);

        return $result->count;
    }

    /**
     * @param array $filter
     *
     * @return JBDatabaseQuery
     */
    protected function _getSelectByCond($filter = array())
    {
        $filter = $this->app->data->create($filter);

        $select = $this->_getSelect()
            ->select('*')
            ->from(ZOO_TABLE_JBZOO_ORDER, 'tOrder');

        if ($filter->get('status')) {
            $select->where('tOrder.status = ?', $filter->get('status'));
        }

        if ((int)$filter->get('created_by') > 0) {
            $select->where('tOrder.created_by = ?', (int)$filter->get('created_by'));
        }

        $totalFrom = $this->app->jbmoney->clearValue($filter->get('total_from'));
        if ($totalFrom > 0) {
            $select->where('tOrder.total >= ?', $totalFrom);
        }

        $totalTo = $this->app->jbmoney->clearValue($filter->get('total_to'));
        if ($totalTo > 0) {
            $select->where('tOrder.total <= ?', $totalTo);
        }

        if ($filter->get('created_from') && $date = $this->app->jbdate->toMysql($filter->get('created_from'))) {
            $select->where('tOrder.created <= ?', $date);
        }

        if ($filter->get('created_to') && $date = $this->app->jbdate->toMysql($filter->get('created_to'))) {
            $select->where('tOrder.created <= ?', $date);
        }

        if ($search = $filter->get('search')) {
            if ((int)$search) {
                $select->where('tOrder.id = ?', (int)$search);
            } else {
                $select->where('tOrder.comment LIKE ' . $this->_db->quote('%' . JString::trim($filter->get('search')) . '%'));
            }
        }

        // set limit
        $limit  = (int)$filter->get('limit', $this->app->system->config->get('list_limit'));
        $offset = (int)$filter->get('offset', 0);
        if ($limit > 0) {
            $select->limit($limit, $offset);
        }

        // set order
        $select->order('tOrder.' . $filter->get('field', 'id') . ' ' . $filter->get('dir', 'DESC'));

        return $select;
    }

}
