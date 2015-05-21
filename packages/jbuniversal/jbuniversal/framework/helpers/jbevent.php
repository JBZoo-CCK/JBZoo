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

// no direct access
defined('_JEXEC') or die('Restricted access');

/**
 * Class JBEventHelper
 */
class JBEventHelper extends AppHelper
{

    const EVENT_BREAK = '__BREAK__';

    /**
     * @var EventHelper
     */
    protected $_event;

    /**
     * @var JBCartPositionHelper
     */
    protected $_position;

    /**
     * @var JBCartElementHelper
     */
    protected $_element;

    /**
     * Array of events
     *      Keys   - name of event
     *      Values - name of positions
     * @var array
     */
    protected $_eventList = array(

        // model
        'basket:saved'           => array(
            'name' => 'order_saved',
        ),
        'basket:beforesave'      => array(
            'name' => 'order_beforesave',
        ),
        'basket:updated'         => array(
            'name' => 'order_updated',
        ),
        'basket:aftersave'       => array(
            'name' => 'order_aftersave',
        ),

        // payment
        'basket:paymentsuccess'  => array(
            'name' => 'order_paymentsuccess',
        ),
        'basket:paymentfail'     => array(
            'name' => 'order_paymentfail',
        ),
        'basket:paymentcallback' => array(
            'name' => 'order_paymentcallback',
        ),

        // statuses
        'basket:orderstatus'     => array(
            'name'   => 'order_orderstatus',
            'status' => JBCart::STATUS_ORDER,
        ),
        'basket:paymentstatus'   => array(
            'name'   => 'order_paymentstatus',
            'status' => JBCart::STATUS_PAYMENT,
        ),
        'basket:shippingstatus'  => array(
            'name'   => 'order_shippingstatus',
            'status' => JBCart::STATUS_SHIPPING,
        ),

        // cart before save
        'basket:additem'         => array(
            'name' => 'order_additem',
        ),
        'basket:updateitem'      => array(
            'name' => 'order_updateitem',
        ),
        'basket:changequantity'  => array(
            'name' => 'order_changequantity',
        ),
        'basket:recount'         => array(
            'name' => 'order_recount',
        ),
        'basket:removeitem'      => array(
            'name' => 'order_removeitem',
        ),
        'basket:removeitems'     => array(
            'name' => 'order_removeitems',
        ),

    );

    /**
     * Class constructor
     * @param App $app
     */
    public function __construct($app)
    {
        parent::__construct($app);

        $this->_event    = $this->app->event;
        $this->_position = $this->app->jbcartposition;
        $this->_element  = $this->app->jbcartelement;
    }

    /**
     * @return array
     */
    public function getEventsName()
    {
        $result = array();
        foreach ($this->_eventList as $eventData) {
            $result[] = $eventData['name'];
        }

        return $result;
    }

    /**
     * @param mixed  $subject
     * @param string $eventName
     * @param array  $params
     */
    public function fire($subject, $eventName, $params = array())
    {
        $eventName = trim($eventName);
        $appEvent  = $this->app->event->create($subject, $eventName, $params);
        $this->app->event->dispatcher->notify($appEvent);
    }

    /**
     * Execute all elements for system event (zoo trigger)
     * @param AppEvent $event
     * @return void
     */
    public function fireElements(AppEvent $event)
    {
        $eventName = $this->app->jbvars->lower($event->getName(), true);

        // simple check
        if (!$eventName || !isset($this->_eventList[$eventName])) {
            return;
        }

        // prepare vars
        $eventData = $this->_eventList[$eventName];
        $order     = $event->getSubject();
        $params    = $event->getParameters();

        // system events
        $cartConf = JBModelConfig::model()->getGroup('cart.' . JBCart::CONFIG_NOTIFICATION);
        $elements = $cartConf->get($eventData['name'], array());
        $this->_execElements($event, $elements, $order, $params);

        // status events
        if (isset($eventData['status']) && $eventData['status']) {
            $cartConf = JBModelConfig::model()->getGroup('cart.' . JBCart::CONFIG_STATUS_EVENTS);
            $elements = $cartConf->get($eventData['status'] . '__' . $params['newStatus'], array());
            $this->_execElements($event, $elements, $order, $params);
        }
    }

    /**
     * Execute all elements in the event position
     * @param AppEvent    $event
     * @param array       $elements
     * @param JBCartOrder $order
     * @param array       $params
     */
    protected function _execElements($event, $elements, $order, $params)
    {
        $elements = (array)$elements;
        if (empty($elements)) {
            return;
        }

        foreach ($elements as $config) {
            // try to create order object
            $element = $this->_element->create($config['type'], $config['group'], $config);
            if (!$element) {
                continue;
            }

            // try to set order object
            if ($order && method_exists($element, 'setOrder') && $order instanceof JBCartOrder) {
                $element->setOrder($order);
            }

            // try to set order object
            if ($order && method_exists($element, 'setEvent')) {
                $element->setEvent($event);
            }

            // check is event available
            if (method_exists($element, 'hasValue') && !$element->hasValue($params)) {
                return;
            }

            // try to execute the element
            if (method_exists($element, 'notify')) {

                try {
                    $elemResult = $element->notify($params);
                } catch (JBCartOrderException $e) {
                    $this->app->jbnotify->warning(JText::_($e->getMessage()));
                }

                if (self::EVENT_BREAK == $elemResult) {
                    return; // break event chain
                }

            }
        }
    }

}
