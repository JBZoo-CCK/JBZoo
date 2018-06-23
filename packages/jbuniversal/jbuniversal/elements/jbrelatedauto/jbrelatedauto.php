<?php
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
 * Class ElementJBRelatedAuto
 */
class ElementJBRelatedAuto extends Element
{
    /**
     * Related items
     * @var array
     */
    protected $_relatedItems = null;

    /**
     * Check, is has value
     * @param array $params
     * @return bool
     */
    public function hasValue($params = [])
    {

        if ((int)$this->get('value', 1)) {
            $items = $this->_getRelatedAuto($params);
            return !empty($items);
        }

        return false;
    }

    /**
     * Edit action
     * @return bool
     */
    public function edit()
    {
        return $this->app->html->_('select.booleanlist', $this->getControlName('value'), '', $this->get('value', 1));
    }

    /**
     * Get related items
     * @param $params
     * @return array|null
     */
    private function _getRelatedAuto($params)
    {

        if ($this->_relatedItems === null) {

            $item = $this->getItem();
            $model = JBModelRelated::model();

            $this->_relatedItems = $model->getRelated($item, $this->config, $params);

            return $this->_relatedItems;
        }

        return $this->_relatedItems;
    }

    /**
     * Render action
     * @param array $params
     * @return mixed
     */
    public function render($params = [])
    {
        // init vars
        $params = $this->app->data->create($params);

        $items = $this->_getRelatedAuto($params);

        $renderer = $this->app->renderer->create('item')->addPath([
            $this->app->path->path('component.site:'),
            $this->_item->getApplication()->getTemplate()->getPath()
        ]);

        // create output
        $layout = $params->get('layout');
        $itemsOutput = [];

        foreach ($items as $item) {

            if ($layout) {
                $itemsOutput[] = $this->app->jblayout->renderItem($item, $layout, $renderer);

            } elseif ($params->get('link_to_item', false) && $item->getState()) {
                $itemsOutput[] = '<a href="' . $this->app->route->item($item) . '" title="' . $item->name . '">' . $item->name . '</a>';

            } else {
                $itemsOutput[] = $item->name;
            }

        }

        $columns = $params->get('columns', 1);
        if ($columns > 0 && $params->get('layout', false)) {
            if ($layout = $this->getLayout()) {
                return self::renderLayout(
                    $layout, [
                        'items'   => $itemsOutput,
                        'columns' => $columns
                    ]
                );
            }
        }

        return $this->app->element->applySeparators($params->get('separated_by'), $itemsOutput);
    }

    /**
     * Sort items
     * @param array $items
     * @param mixed $order
     * @return array
     */
    protected function _orderItems($items, $order)
    {
        // if string, try to convert ordering
        if (is_string($order)) {
            $order = $this->app->itemorder->convert($order);
        }

        $items = (array)$items;
        $order = (array)$order;
        $sorted = [];
        $reversed = false;

        // remove empty values
        $order = array_filter($order);

        // if random return immediately
        if (in_array('_random', $order)) {
            shuffle($items);

            return $items;
        }

        // get order dir
        if (($index = array_search('_reversed', $order)) !== false) {
            $reversed = true;
            unset($order[$index]);
        } else {
            $reversed = false;
        }

        // order by default
        if (empty($order)) {
            return $reversed ? array_reverse($items, true) : $items;
        }

        // if there is a none core element present, ordering will only take place for those elements
        if (count($order) > 1) {
            $order = array_filter($order, function ($a) {
                return strpos($a, '_item') === false;
            });
        }

        if (!empty($order)) {

            // get sorting values
            foreach ($items as $item) {
                foreach ($order as $identifier) {
                    if ($element = $item->getElement($identifier)) {
                        $sorted[$item->id] =
                            strpos($identifier, '_item') === 0
                                ? $item->{str_replace('_item', '', $identifier)}
                                : $element->getSearchData();
                        break;
                    }
                }
            }

            // do the actual sorting
            $reversed ? arsort($sorted) : asort($sorted);

            // fill the result array
            foreach (array_keys($sorted) as $id) {
                if (isset($items[$id])) {
                    $sorted[$id] = $items[$id];
                }
            }

            // attach unsorted items
            $sorted += array_diff_key($items, $sorted);


            // no sort order provided
        } else {
            $sorted = $items;
        }

        return $sorted;
    }

    /**
     * Get config form
     * @return mixed
     */
    public function getConfigForm()
    {
        return parent::getConfigForm()->addElementPath(dirname(__FILE__));
    }

}
