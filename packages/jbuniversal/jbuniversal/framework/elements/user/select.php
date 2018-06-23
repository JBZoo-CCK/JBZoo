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
 * Class JBCSVItemUserSelect
 */
class JBCSVItemUserSelect extends JBCSVItem
{
    /**
     * @return string
     */
    public function toCSV()
    {
        if (isset($this->_value['option'])) {

            if (is_array($this->_value['option'])) {
                return implode(JBCSVItem::SEP_CELL, $this->_value['option']);
            } else {
                return $this->_value['option'];
            }

        }

        return null;
    }

    /**
     * @param $value
     * @param null $position
     * @return Item|void
     */
    public function fromCSV($value, $position = null)
    {
        $options    = $this->_getArray($value, JBCSVItem::SEP_CELL);
        $importData = $this->_lastImportParams->get('previousparams');

        foreach ($options as $key => $option) {

            if (isset($importData['checkOptions']) && (int)$importData['checkOptions'] == JBImportHelper::OPTIONS_YES) {
                $this->app->jbtype->checkOption($option, $this->_identifier, $this->_item->getType()->id, $this->_item->application_id);
            }

            $options[$key] = $this->app->string->sluggify($option);
        }

        $this->_element->bindData(array('option' => $options));

        return $this->_item;
    }
}
