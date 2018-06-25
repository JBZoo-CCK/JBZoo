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
 * Class JBCartElementHookJBAdvert
 */
class JBCartElementHookJBAdvert extends JBCartElementHook
{
    /**
     * @param array $params
     */
    public function notify($params = array())
    {
        $items = (array)$this->getOrder()->getItems();
        if (empty($items)) {
            return;
        }

        foreach ($items as $orderData) {

            if ($orderData->get('advert_id') != $this->config->get('advert_id')) {
                continue;
            }

            /** @type Item $item */
            $item = $this->app->table->item->get($orderData->get('item_id'));
            if (!$item) {
                continue;
            }

            /** @type ElementJBAdvert $element */
            $element = $item->getElement($orderData->get('advert_id'));
            if (!$element || !is_a($element, 'ElementJBAdvert')) {
                continue;
            }

            $element->modifyItem($orderData, $this->getOrder());
            //$this->app->table->item->unsetObject($item->id);
            //unset($item);
        }

    }
}
