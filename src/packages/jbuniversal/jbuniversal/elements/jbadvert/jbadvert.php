<?php
use Joomla\String\StringHelper;
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

/**
 * Class ElementJBAdvert
 */
class ElementJBAdvert extends Element implements iSubmittable
{

    const MODE_PUBLISH    = 'publish';
    const MODE_PRIORITY   = 'priority';
    const MODE_CATEGORY   = 'category';
    const MODE_EXPIREDATE = 'expiredate';
    const MODE_ELEMENT    = 'element';
    const MODE_PHP        = 'php';

    const MODIFIED_NO   = 0;
    const MODIFIED_YES  = 1;
    const MODIFIED_EXEC = 2;

    /**
     * @type string
     */
    protected $_sesKey = 'data';

    /**
     * @param array $params
     * @return bool
     */
    public function hasValue($params = [])
    {
        return false;
    }

    /**
     * @param array $params
     * @return null|string
     */
    public function edit($params = [])
    {
        JFactory::getSession()->set($this->_getSaveKey(), $this->data(), __CLASS__);

        if ($layout = $this->getLayout('edit.php')) {
            return self::renderLayout($layout, [
                'params' => $params,
            ]);
        }

        return false;
    }

    /**
     * Render submission
     * @param array $params
     * @return null|string
     */
    public function renderSubmission($params = [])
    {
        $elementLayout = 'submission-not-modified.php';
        if ($this->_isModified()) {
            $elementLayout = 'submission-modified.php';
        }

        if ($layout = $this->getLayout($elementLayout)) {
            return self::renderLayout($layout, [
                'params' => $params,
            ]);
        }

        return false;
    }

    /**
     * Validate submission
     * @param $values
     * @param $params
     * @return mixed
     * @throws AppValidatorException
     */
    public function validateSubmission($values, $params)
    {

        $mode = $this->_getMode();
        $price = $this->_getPrice()->data(true);
        $mParams = $this->_getModifiersParams($mode);

        $values = [
            'mode'        => $mode,
            'price'       => $price,
            'params'      => $mParams,
            'order_id'    => 0,
            'modified'    => $this->_getLastModified(),
            'is_modified' => 0,
        ];

        return $values;
    }

    /**
     * @return string
     */
    protected function _getLastModified()
    {
        $date = $this->get('modified', JText::_('JBZOO_JBADVERT_MODIFIED_DATE_UNDEFIED'));
        return $this->app->jbdate->toHuman($date);
    }

    /**
     * @param array $data
     */
    public function bindData($data = [])
    {
        $saveData = $data;
        if ($this->getItem()) {
            $newData = JFactory::getSession()->get($this->_getSaveKey(), null, __CLASS__);
        }

        if (!empty($newData)) {
            $saveData = $newData;
        }

        $saveData = array_merge($saveData, $data);
        if ($saveData['is_modified'] == self::MODIFIED_EXEC) {
            parent::bindData($saveData);
            $this->modifyItem();
        } else {
            parent::bindData($saveData);
        }

    }

    /**
     * Modify item
     * @param JSONData|array   $params
     * @param JBCartOrder|null $order
     * @return bool
     */
    public function modifyItem($params = null, $order = null)
    {
        if ($this->_isModified()) {
            return false;
        }

        $item = $this->getItem();
        if (empty($item)) {
            return false;
        }

        $mode = $this->_getMode();
        /** @type JSONData $mParams */
        $mParams = $this->app->data->create($this->get('params', []));
        /** @type JBDateHelper $jbdate */
        $jbdate = $this->app->jbdate;

        if ($mode == self::MODE_PUBLISH) { // publish
            $item->setState(1, false);


        } else {
            if ($mode == self::MODE_CATEGORY) { // change category and application

                $item = $this->getItem();
                $itemId = $this->getItem()->id;
                $appId = (int)$mParams->find('item_category_value.app', 0);
                if ($appId == 0) {
                    $appId = $item->application_id;
                }
                $categories = $item->getRelatedCategoryIds();
                $categories[] = (int)$mParams->find('item_category_value.category', 0);
                $categories = array_unique($categories);
                $item->application_id = $appId;
                $this->app->category->saveCategoryItemRelations($item, $categories);
                define('JBADVERT_EVALED', true);
                // TODO admin page save bug

            } else {
                if ($mode == self::MODE_ELEMENT) {  // set new data to element
                    /** @type Element|ElementRepeatable $element */
                    $element = $item->getElement($mParams->get('item_element_id'));
                    if ($element) {

                        $value = StringHelper::trim($mParams->get('item_element_value'));

                        if (strpos($value, '{') === 0 && strpos($value, '}') > 1) {
                            $value = json_decode($value, true);
                        } else {
                            if ($element instanceof ElementOption) {
                                $value = ['option' => [$this->app->string->sluggify($value)]];
                            } else {
                                if ($element instanceof ElementRepeatable) {
                                    $value = [['value' => $value]];
                                } else {
                                    $value = ['value' => $value];
                                }
                            }
                        }

                        $element->bindData($value);
                    }


                } else {
                    if ($mode == self::MODE_PHP) { // execute php code

                        $edit = (bool)$this->getItem()->id;
                        $item = $this->getItem();
                        $itemId = $this->getItem()->id;
                        eval($this->config->get('item_php_eval'));
                        define('JBADVERT_EVALED', true);

                    } else {
                        if ($mode == self::MODE_PRIORITY) { // change priority
                            $item->priority = (int)$mParams->get('item_priority_value', -1);


                        } else {
                            if ($mode == self::MODE_EXPIREDATE) { // set new expire date (publish date)

                                $addTime = 86400 * $this->app->jbvars->number(
                                        $mParams->get('item_expiredate_timeout', 30)
                                    );

                                $oldDate = time();

                                if ($time = strtotime($item->publish_down)) {
                                    if ($time > 0) {
                                        $oldDate = $time;
                                    }
                                }

                                $item->setState(1, false);
                                $item->publish_down = $jbdate->toMysql($oldDate + $addTime);
                            }
                        }
                    }
                }
            }
        }

        $this->set('is_modified', self::MODIFIED_YES);
        $this->set('modified', $jbdate->toMysql(time()));
        if ($order && $order->id) {
            $this->set('order_id', $order->id);
        }

        $this->app->table->item->save($item);

        return true;
    }

    /**
     * @return mixed
     */
    public function _renderModifierParams()
    {
        $htmlParams = [];
        $mParams = $this->get('params', []);

        foreach ($mParams as $key => $value) {

            if ($key == 'item_element_value') {
                //$value = htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
                //$value = '<pre>' . $value . '</pre>';
                continue;

            } else {
                if ($key == 'item_php_eval') {
                    //$value = '<pre>' . $value . '</pre>';
                    continue;

                } else {
                    if ($key == 'item_element_id') {
                        if ($element = $this->getItem()->getElement($value)) {
                            $value = $element->config->get('name') ? $element->config->get('name') : $element->config->get('type');
                        }

                    } else {
                        if ($key == 'item_category_value') {

                            if ($application = $this->app->table->application->get($value['app'])) {
                                $htmlParams['JBZOO_JBADVERT_EDIT_APP'] = $application->name;
                            }

                            if ($value['category'] == 0) {
                                $htmlParams['JBZOO_JBADVERT_EDIT_CATEGORY'] = JText::_('JBZOO_JBADVERT_EDIT_CATEGORY_ROOT');
                            } else {
                                if ($category = $this->app->table->category->get($value['category'])) {
                                    $htmlParams['JBZOO_JBADVERT_EDIT_CATEGORY'] = $category->name;
                                }
                            }

                        }
                    }
                }
            }

            if (!empty($value) && !is_array($value)) {
                $htmlParams['JBZOO_JBADVERT_EDIT_' . $key] = $value;
            }
        }

        return $this->app->jbhtml->dataList($htmlParams);
    }

    /**
     * @return bool
     */
    protected function _isModified()
    {
        return (int)$this->get('is_modified', self::MODIFIED_NO) == self::MODIFIED_YES;
    }

    /**
     * @return array
     */
    protected function _getModifiersParams()
    {
        $mode = $this->_getMode();

        $result = [];
        foreach ($this->config as $key => $value) {
            if (strpos($key, 'item_' . $mode) === 0) {
                $result[$key] = $value;
            }
        }

        return $result;
    }

    /**
     * Add to cart
     * @return $this
     */
    public function addToCart()
    {
        JBCart::getInstance()->addItem($this->_getCartData());
        return $this;
    }

    /**
     * @return array
     */
    protected function _getCartData()
    {
        $price = $this->_getPrice();
        $item = $this->getItem();

        $data = [
            'key'        => md5($this->identifier . serialize($this->_getModifiersParams())),
            'item_id'    => $item->id,
            'item_name'  => $item->name,
            'advert_id'  => $this->identifier,
            'element_id' => $this->identifier,
            'total'      => $price->data(true),
            'values'     => $this->_getCartValues(),
            'quantity'   => 1,
            'variant'    => 0,
            'elements'   => [
                '_value' => $price->data(true)
            ],
            'params'     => [
                '_quantity' => [
                    'min'      => 1,
                    'max'      => 1,
                    'step'     => 1,
                    'default'  => 1,
                    'decimals' => 0
                ]
            ],
        ];

        $data = $this->app->data->create($data);

        return $data;
    }

    /**
     * @return array
     */
    protected function _getCartValues()
    {
        $modName = JText::_('JBZOO_JBADVERT_MODE_' . $this->_getMode());
        $elementName = $this->config->get('name', $modName);

        return [
            JText::_('JBZOO_JBADVERT_CART_MODE') => $elementName ? $elementName : $modName,
        ];
    }

    /**
     * Hack! Compatible with JBCart API
     * @param $key
     */
    public function setDefault($key)
    {
        //$this->set('default_variant', $key);
    }

    /**
     * Hack! Compatible with JBCart API
     */
    public function inStock()
    {
        return 1;
    }

    /**
     * Get total sum
     * @return JBCartValue
     */
    protected function _getPrice()
    {
        $price = $this->config->get('price', 0);
        $order = $this->_getRelatedOrder();

        $result = JBCart::val($price);
        if ($order && $order->id) {
            $result = $order->val($price);
        }

        return $result;
    }

    /**
     * @return mixed
     */
    protected function _getMode()
    {
        return $this->config->get('mode', self::MODE_PUBLISH);
    }

    /**
     * @param bool $isLink
     * @return JBCartOrder|string
     */
    protected function _getRelatedOrder($isLink = false)
    {
        $order = JBModelOrder::model()->getById($this->get('order_id', 0));

        if ($isLink) {
            $orderLink = JText::sprintf('JBZOO_JBDVERT_ORDER_UNDEFINED', $this->get('order_id', 0));
            if ($order && $order->id) {
                $orderLink = $order->getUrl(null, 'full');
            }

            return $orderLink;
        }

        return $order;
    }

    /**
     * @return $this
     */
    public function loadAssets()
    {
        $this->app->jbassets->less('elements:jbadvert/assets/less/jbadvert.less');
        $this->app->jbassets->js('elements:jbadvert/assets/js/jbadvert.js');

        return parent::loadAssets();
    }

    /**
     * @return string
     */
    protected function _getSaveKey()
    {
        if ($item = $this->getItem()) {
            return $this->_sesKey = 'data-' . $item->id . '-' . $this->identifier;
        }

        return $this->identifier;
    }

}
