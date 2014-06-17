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
 * Class JBCSVItemUserRadio
 */
class JBCSVItemUserRadio extends JBCSVItem
{
    /**
     * @return null|string
     */
    public function toCSV()
    {
        if (isset($this->_value['option'])) {
            return $this->_value['option'];
        }

        return '';
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
