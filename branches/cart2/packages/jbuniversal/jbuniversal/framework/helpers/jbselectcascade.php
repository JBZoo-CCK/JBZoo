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
 * Class JBSelectCascadeHelper
 */
class JBSelectCascadeHelper extends AppHelper
{

    /**
     * Get item list
     * @param string $names
     * @param string $items
     * @return array
     */
    public function getItemList($names, $items)
    {
        $configNames = $this->parseLines($names);
        $configItems = $this->parseLines($items);

        $maxLevel    = 0;
        $resultItems = array();

        if (!empty($configItems)) {

            $prevLevel     = 0;
            $prevLevelName = '';

            $nestedKeys = array();

            foreach ($configItems as $configItem) {

                if (preg_match("#^([- ]*|[-]*)(.*)#ius", $configItem, $matches)) {

                    $level = substr_count(trim($matches[1]),'-');

                    if ($prevLevel < $level) {
                        $nestedKeys[] = $prevLevelName;

                    } elseif ($prevLevel > $level) {

                        for ($i = 1; $i <= $prevLevel - $level; $i++) {
                            array_pop($nestedKeys);
                        }
                    }

                    if (count($nestedKeys) > $maxLevel) {
                        $maxLevel = count($nestedKeys);
                    }

                    $listTitle = ' ';
                    if (isset($configNames[$level])) {
                        $listTitle = $configNames[$level];
                    }

                    $resultItems = $this->_addToNestedList($matches[2], $resultItems, $nestedKeys, $listTitle);

                    $prevLevelName = $matches[2];

                    $prevLevel     = $level;
                }
            }
        }

        $result = array(
            'items'    => $resultItems,
            'names'    => $configNames,
            'maxLevel' => $maxLevel
        );

        return $result;
    }

    /**
     * Parse text by lines
     * @param string $text
     * @return array
     */
    public function parseLines($text)
    {
        $text = JString::trim($text);
        $text = htmlspecialchars_decode($text, ENT_COMPAT);
        $text = strip_tags($text);

        $lines = explode("\n", $text);

        $result = array();
        if (!empty($lines)) {
            foreach ($lines as $line) {
                $line     = JString::trim($line);
                $result[] = strtr($line, "\"", "'");
            }
        }

        return $result;
    }

    /**
     * Add item to nested list
     * @param string $item
     * @param array $resultArr
     * @param array $nestedKeys
     * @param string $listTitle
     * @return array
     */
    protected function _addToNestedList($item, array $resultArr, array $nestedKeys, $listTitle)
    {
        $tmpArr = & $resultArr;

        if (!empty($nestedKeys)) {
            foreach ($nestedKeys as $nestedKey) {
                $tmpArr = & $tmpArr[$nestedKey];
            }
        }

        $tmpArr[$item] = array();

        return $resultArr;
    }

}
