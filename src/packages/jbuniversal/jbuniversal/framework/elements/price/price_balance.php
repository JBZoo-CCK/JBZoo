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
 * Class JBCSVItemPricePrice_balance
 */
class JBCSVItemPricePrice_balance extends JBCSVItem
{
    /**
     * @return string|void
     */
    public function toCSV()
    {
        $priceElements = $this->_element;
        if (!empty($priceElements)) {
            $data = $priceElements->data();
            if (!empty($data)) {
                $basic = $data['basic'];
            }
            return isset($basic['balance']) ? $basic['balance'] : -1;
        }

        return null;

    }

    /**
     * @param  $value
     * @param  null $variant
     * @return Item|void
     */
    public function fromCSV($value, $variant = null)
    {
        // save data
        $data = $this->_element->data();

        if ($variant == 0) {
            $data['basic']['params']['_balance'] = isset($value) ? $value : -1;

        } elseif ($variant >= 1) {
            $data['variations'][$variant]['params']['_balance'] = isset($value) ? $value : '';

        }

        $this->_element->bindData($data);

        return $this->_item;
    }

}