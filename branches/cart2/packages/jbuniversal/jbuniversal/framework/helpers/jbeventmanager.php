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

/**
 * Class JBEventManagerHelper
 */
class JBEventManagerHelper extends AppHelper
{
    /**
     * @var EventHelper
     */
    protected $_event;

    /**
     * @var JBCartPositionHelper
     */
    protected $_position;

    /**
     * Array of events
     * Keys   - name of event
     * Values - name of positions
     *
     * @var array
     */
    protected $_events = array(
        'basket:saved'      => self::NOTIFY_ORDER_CREATE,
        'basket:beforesave' => self::NOTIFY_ORDER_BEFORESAVE,
        'order:edit'        => self::NOTIFY_ORDER_EDIT,
        'order:status'      => self::NOTIFY_ORDER_STATUS,
        'order:payment'     => self::NOTIFY_ORDER_PAYMENT,
        'comment:saved'     => self::NOTIFY_NEW_COMMENT
    );

    const NOTIFY_ORDER_CREATE     = 'order_create';
    const NOTIFY_ORDER_BEFORESAVE = 'order_beforesave';
    const NOTIFY_ORDER_EDIT       = 'order_edit';
    const NOTIFY_ORDER_STATUS     = 'order_status';
    const NOTIFY_ORDER_PAYMENT    = 'order_payment';
    const NOTIFY_NEW_COMMENT      = 'new_comment';

    /**
     * Class constructor
     *
     * @param App $app
     */
    public function __construct($app)
    {
        parent::__construct($app);

        $this->_event    = $this->app->event;
        $this->_position = $this->app->jbcartposition;
    }

    /**
     * Return  array of events - $this->_events
     * @return array
     */
    public function getEventsName()
    {
        return array_values($this->_events);
    }

    /**
     * Add listeners from array $_events
     * @return void
     */
    public function fireListeners()
    {
        $events = array_keys($this->_events);

        foreach ($events as $event) {
            $this->app->event->dispatcher->connect($event, array($this, 'notify'));
        }
    }

    /**
     * Check event name with position name and call element
     * if they are equal.
     *
     * @param AppEventDispatcher $event
     *
     * @return bool
     */
    public function notify($event)
    {
        $name = $event->getName();
        if (array_key_exists($name, $this->_events)) {
            if ($positions = $this->_position->loadPositions(JBcart::CONFIG_NOTIFICATION)) {
                $key = $this->_events[$name];
                if (array_key_exists($key, $positions)) {
                    $subject = $event->getSubject();
                    if (!$subject->id) {
                        return false;
                    }

                    foreach ($positions[$key] as $element) {
                        $element->setSubject($subject);
                        $element->notify();
                    }
                }
            }
        }

        return true;
    }

}
