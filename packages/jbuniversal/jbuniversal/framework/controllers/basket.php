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
 * Class BasketJBUniversalController
 */
class BasketJBUniversalController extends JBUniversalController
{

    const TIME_BETWEEN_PUBLIC_SUBMISSIONS = 10;
    const SESSION_PREFIX                  = 'JBZOO_';

    /**
     * @var JBModelConfig
     */
    protected $_config = null;

    /**
     * @var JBCartHelper
     */
    protected $_jbcart = null;

    /**
     * @param array $app
     * @param array $config
     */
    public function __construct($app, $config = array())
    {
        parent::__construct($app, $config);

        $this->app->jbdoc->noindex();
        $this->_config = JBModelConfig::model()->getGroup('cart.config');
    }

    /**
     * Filter action
     * @throws AppException
     */
    function index()
    {
        // init
        $this->app->jbdebug->mark('basket::init');

        $basketItems = $this->app->jbcart->getBasketItems();

        $this->template = $this->application->getTemplate();

        $form = new JBCartForm($this->app);

        $this->renderer = $this->app->renderer->create('basket')->addPath(array(
            $this->app->path->path('component.site:'),
            $this->template->getPath()
        ));

        dump($this->app->path->_paths, 0);

        dump($this->renderer->render('cart.form'));


        // get items
        $itemIds = $this->app->jbcart->getItemIds($isAdvance);
        $items   = JBModelFilter::model()->getZooItemsByIds($itemIds);

        if (!JFactory::getUser()->id && (int)$appParams->get('global.jbzoo_cart_config.auth', 0)) {
            $this->setRedirect(JRoute::_($this->app->jbrouter->auth(), false), JText::_('JBZOO_AUTH_PLEASE'));
        }

        $this->basketItems = $basketItems;
        $this->params      = $this->_params;
        $this->items       = $items;
        $this->appId       = $appId;
        $this->Itemid      = $Itemid;
        $this->errors      = array();
        $this->appParams   = $appParams;
        $this->isAdvance   = $isAdvance;

        $this->item = $this->_createEmptyItem($this->type);

        // get submition
        $this->submission = $this->app->table->submission->get((int)$submissionId);

        if ($this->submission) {

            $this->application = $this->submission->getApplication();

            $layout     = $this->submission->getForm($this->type->id)->get('layout', '');
            $layoutPath = $this->application->getGroup() . '.' . $this->type->id . '.' . $layout;
            $positions  = $this->renderer->getConfig('item')->get($layoutPath, array());

            // get elements from positions
            $elementsConfig = array();
            foreach ($positions as $position) {
                foreach ($position as $element) {
                    if (isset($element['element'])) {
                        $elementsConfig[$element['element']] = $element;
                    }
                }
            }

            $this->template = $this->application->getTemplate();
            $sessionFormKey = self::SESSION_PREFIX . 'SUBMISSION_FORM_' . $this->submission->id;
            if ($post = unserialize($this->app->system->application->getUserState($sessionFormKey))) {
                $this->app->system->application->setUserState($sessionFormKey, null);
                $this->errors = $this->_bind($post, $elementsConfig, $this->item);
            }

        } else {
            $this->app->jbnotify->warning(JText::_('JBZOO_BASKET_SUBMISSION_FORM_IS_NO_SET'));
            return false;
        }

        $this->app->jbdebug->mark('basket::renderInit');
        $this->getView('basket')->addTemplatePath($this->template->getPath())->setLayout('basket')->display();
        $this->app->jbdebug->mark('basket::display');
    }

    /**
     * Delete item action
     */
    public function clear()
    {
        $this->app->jbcart->removeItems();
        $this->app->jbajax->send();
    }

    /**
     * Clear action
     */
    public function delete()
    {
        $itemId    = $this->_jbrequest->get('itemid');
        $hash      = $this->_jbrequest->get('hash');
        $item      = $this->app->table->item->get($itemId);
        $appParams = $this->application->getParams();

        $isAdvance = (int)$appParams->get('global.jbzoo_cart_config.is_advance', 0);

        $this->app->jbcart->removeItem($item, $isAdvance, $hash);
        $recountResult = $this->app->jbcart->recount($appParams);

        $this->app->jbajax->send($recountResult);
    }

    /**
     * Reload module action
     */
    public function reloadModule()
    {
        $moduleId = $this->_jbrequest->get('moduleId');
        $html     = $this->app->jbjoomla->renderModuleById($moduleId);

        header('Content-Type: text/html; charset=utf-8'); // fix apache default charset
        jexit($html);
    }

    /**
     * Quantity action
     */
    public function quantity()
    {
        $appParams = $this->application->getParams();
        $isAdvance = (int)$appParams->get('global.jbzoo_cart_config.is_advance', 0);

        // get request
        $value  = (int)$this->_jbrequest->get('value');
        $itemId = (int)$this->_jbrequest->get('itemId');
        $hash   = trim($this->_jbrequest->get('hash'));

        // get product item
        $item = $this->app->table->item->get($itemId);

        if ($isAdvance) {

            $jbPrices = $item->getElementsByType('jbpriceadvance');
            if (!empty($jbPrices)) {
                $jbPrice = current($jbPrices);

                if ($jbPrice->isInStock($hash, $value)) {
                    $this->app->jbcart->changeQuantity($item, $value, $hash, $isAdvance);
                    $recountResult = $this->app->jbcart->recount($appParams, $isAdvance);
                    $this->app->jbajax->send($recountResult);

                } else {
                    $this->app->jbajax->send(array('message' => JText::_('JBZOO_JBPRICE_NOT_AVAILABLE_MESSAGE')), false);
                }
            }
        }

        $this->app->jbcart->changeQuantity($item, $value, $hash, $isAdvance);
        $recountResult = $this->app->jbcart->recount($appParams, $isAdvance);

        $this->app->jbajax->send($recountResult);
    }

    /**
     * Create order action
     */
    public function createOrder()
    {
        $this->app->request->checkToken() or jexit('Invalid Token');

        $post   = $this->app->request->get('post:', 'array');
        $appId  = $this->_jbrequest->get('app_id');
        $Itemid = $this->_jbrequest->get('Itemid');

        try {
            $application = $this->app->table->application->get($appId);

            if (!$application) {
                throw new AppException('AppId is no set');
            }

            $appParams = $this->application->getParams();
            list($type, $layoutPath) = explode(':', $appParams->get('global.jbzoo_cart_config.type-layout'));

            $this->type = $application->getType($type);

            $item = $this->_createEmptyItem($this->type, $application);

            if (!$this->type) {
                throw new AppException('Type is not defined');
            }

            $this->template = $application->getTemplate();
            $this->renderer = $this->app->renderer->create('basket')->addPath(array(
                $this->app->path->path('component.site:'),
                $this->template->getPath()
            ));

            $submissionId = $appParams->get('global.jbzoo_cart_config.submission-id');
            $submission   = $this->app->table->submission->get($submissionId);
            $layout       = $submission->getForm($this->type->id)->get('layout', '');
            $layoutPath   = $application->getGroup() . '.' . $this->type->id . '.' . $layout;
            $positions    = $this->renderer->getConfig('item')->get($layoutPath, array());

            // get elements from positions
            $elementsConfig = array();
            foreach ($positions as $position) {
                foreach ($position as $element) {
                    $elementsConfig[$element['element']] = $element;
                }
            }

            if (isset($post['elements'])) {
                $this->app->request->setVar('elements', $this->app->submission->filterData($post['elements']));
                $post = $this->app->request->get('post:', 'array');
                $post = array_merge($post, $post['elements']);
            }

            foreach ($_FILES as $key => $userfile) {
                if (strpos($key, 'elements_') === 0) {
                    $post[str_replace('elements_', '', $key)]['userfile'] = $userfile;
                }
            }

            $error = $this->_bind($post, $elementsConfig, $item);

            $sessionFormKey = self::SESSION_PREFIX . 'SUBMISSION_FORM_' . $submission->id;

            $order = JBModelOrder::model()->getDetails($item);
            if ($order) {
                $totalPrice   = $order->getTotalPrice();
                $mimimalPrice = (float)$appParams->get('global.jbzoo_cart_config.minimal-summa', 0);

                if ($mimimalPrice > 0 && $mimimalPrice > $totalPrice) {
                    $this->app->jbnotify->warning(JString::str_ireplace('%S', $mimimalPrice, JText::_('JBZOO_CART_MINIMAL_PRICE_ERROR')));
                    $error = true;
                }
            }

            // save item if it is valid
            if ($error) {
                $this->app->system->application->setUserState($sessionFormKey, serialize($post));
                $this->app->jbnotify->warning(JText::_('JBZOO_CART_SUBMIT_ERRROS'));

            } else {
                $user = JFactory::getUser();

                $nowDate     = $this->app->date->create()->toSql();
                $nowDateTime = new DateTime($nowDate);
                $date        = JHTML::_('date', 'now', JText::_('Y-m-d H:i:s')) . ' (GMT ' . ($nowDateTime->getOffset() / 3600) . ')';

                $item->name        = $this->type->name . ' #__ID__ / ' . $date . ($user->email ? ' / ' . $user->email : '');
                $item->alias       = $this->app->alias->item->getUniqueAlias($item->id, $this->app->string->sluggify($item->name));
                $item->state       = 1;
                $item->modified    = $nowDate;
                $item->modified_by = $user->get('id');

                $timestamp = time();
                if ($timestamp < $this->app->system->session->get('ZOO_LAST_SUBMISSION_TIMESTAMP') + BasketJBUniversalController::TIME_BETWEEN_PUBLIC_SUBMISSIONS) {
                    $this->app->system->application->setUserState($sessionFormKey, serialize($post));
                    throw new AppException('You are submitting too fast, please try again in a few moments.');
                }

                $this->app->system->session->set('ZOO_LAST_SUBMISSION_TIMESTAMP', $timestamp);

                foreach ($elementsConfig as $element) {
                    if (($element = $item->getElement($element['element'])) && $element instanceof iSubmissionUpload) {
                        $element->doUpload();
                    }
                }

                // set category
                $primaryCategory = $item->getPrimaryCategoryId();
                $categoryId      = (int)$submission->getForm($item->type)->get('category', 0);
                if (empty($primaryCategory) && $categoryId > 0) {
                    $item->getParams()->set('config.primary_category', $categoryId);
                }

                // save
                $this->app->event->dispatcher->notify($this->app->event->create($item, 'basket:beforesave', array('item' => $item, 'appParams' => $appParams)));
                $this->app->event->dispatcher->notify($this->app->event->create($submission, 'submission:beforesave', array('item' => $item, 'new' => true)));
                $this->app->table->item->save($item);

                // change name to ID
                $item->name = JString::str_ireplace('__ID__', $item->id, $item->name);
                $this->app->table->item->save($item);

                // save relative category
                if ($categoryId > 0) {
                    $this->app->category->saveCategoryItemRelations($item, array($categoryId));
                }

                // after save event
                $this->app->event->dispatcher->notify($this->app->event->create($item, 'basket:saved', array('item' => $item, 'appParams' => $appParams)));

                // empty cart items
                $this->app->jbcart->removeItems();

                // redirect
                $orderDetails = JBModelOrder::model()->getDetails($item);
                if ((int)$appParams->get('global.jbzoo_cart_config.payment-enabled') && $orderDetails->getTotalPrice() > 0) {
                    $msg = JText::_('JBZOO_CART_SUCCESS_TO_PAYMENT_MESSAGE');
                    $this->setRedirect(JRoute::_($this->app->jbrouter->basketPayment($Itemid, $appId, $item->id), false));

                    return;

                } else {
                    $msg = JText::_('JBZOO_CART_SUCCESS_MESSAGE');
                    $this->setRedirect(JRoute::_($this->app->jbrouter->paymentNotPaid($Itemid, $appId, $item->id), false), $msg);

                    return;
                }
            }

        } catch (AppException $e) {

            $error = true;
            $this->app->jbnotify->warning(JText::_('There was an error saving your submission, please try again later.'));
            $this->app->jbnotify->warning((string)JText::_($e));
        }

        $this->setRedirect(JRoute::_($this->app->jbrouter->basket($Itemid, $appId), false));
    }

    /**
     * Create empty item
     */
    protected function _createEmptyItem($type, $application = null)
    {
        if (!$application) {
            $application = $this->application;
        }

        $user = JFactory::getUser();

        // Joomla ViewLevel (Registered)
        $accessLevel = $user->getAuthorisedViewLevels();
        if (count($accessLevel) > 1) {
            $accessLevel = $accessLevel[1];
        } else if (count($accessLevel) == 1) {
            $accessLevel = $accessLevel[0];
        } else {
            $accessLevel = 1;
        }

        // get item
        $item                   = $this->app->object->create('Item');
        $item->application_id   = $application->id;
        $item->type             = $type->id;
        $item->publish_up       = $this->app->date->create()->toSQL();
        $item->publish_down     = $this->app->database->getNullDate();
        $item->access           = $accessLevel;
        $item->created          = $this->app->date->create()->toSQL();
        $item->created_by       = $user->id;
        $item->created_by_alias = '';
        $item->state            = 0;
        $item->searchable       = 0;
        $item->getParams()
            ->set('config.enable_comments', 1)
            ->set('config.primary_category', 0)
            ->set('metadata.robots', 'noindex, nofollow');

        return $item;
    }

    /**
     * Bind data
     * @param array $post
     * @param array $elementsConfig
     * @param Item $item
     * @return int
     */
    protected function _bind($post, $elementsConfig, $item)
    {
        $errors = 0;

        foreach ($elementsConfig as $elementData) {
            try {

                if (($element = $item->getElement($elementData['element']))) {
                    $params = $this->app->data->create(array_merge(array('trusted_mode' => true), $elementData));
                    $element->bindData($element->validateSubmission($this->app->data->create(@$post[$element->identifier]), $params));
                }

            } catch (AppValidatorException $e) {
                if (isset($element)) {
                    $element->error = $e;
                    $element->bindData(@$post[$element->identifier]);
                }
                $errors++;
            }
        }

        return $errors;
    }

}
