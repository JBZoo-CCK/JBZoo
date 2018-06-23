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

require_once __DIR__ . '/price.php';

/**
 * Class JBCSVItemPriceValue
 */
class JBCSVItemPriceValue extends JBCSVItemPrice
{
    /**
     * @param           $value
     * @param  int|null $variant
     * @return Item|void
     */
    public function fromCSV($value, $variant = 0)
    {
        $modifier   = null;
        $isCurrency = $this->app->jbmoney->checkCurrency($value);

        if ($isCurrency) {
            $val = $this->_element->data()->find('variations.' . $variant . '._value.value');
            if ($val[0] == '-' || $val[0] == '+') {
                $modifier = $val['0'];
            }

            $val   = $this->app->jbmoney->clearValue($val);
            $value = $val . $value;
        }

        return array('value' => $modifier . $value);
    }

}