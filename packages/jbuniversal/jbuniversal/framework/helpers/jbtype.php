<?php
/**
 * JBZoo App is universal Joomla CCK, application for YooTheme Zoo component
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
 * Class JBTypeHelper
 */
class JBTypeHelper extends AppHelper
{

    /**
     * Get simple list - all types in system
     * @return array
     */
    public function getSimpleList()
    {
        $types = array();

        $typePath  = $this->app->path->path('jbtypes:');
        $typeFiles = JFolder::files($typePath, '\.config');

        if (!empty($typeFiles)) {
            foreach ($typeFiles as $file) {

                $fileContent = $this->app->jbfile->read($typePath . '/' . $file);
                $typeData    = json_decode($fileContent, true);

                $alias         = preg_replace('#\.config$#i', '', $file);
                $types[$alias] = $typeData['name'];
            }
        }

        return $types;
    }

    /**
     * @param string|array $newOption
     * @param string       $elementId
     * @param string       $typeId
     * @param int          $appId
     * @return bool
     */
    public function checkOption($newOption, $elementId, $typeId, $appId)
    {
        $application = $this->app->table->application->get($appId);
        if ($application) {
            $type = $application->getType($typeId);
        }

        if (!isset($type)) {
            return false;
        }

        $elements      = $type->config->get('elements', array());
        $newOptionSlug = $this->app->string->sluggify($newOption);

        // check is valid element structure
        if (!isset($elements[$elementId]['option']) || !is_array($elements[$elementId]['option'])) {
            return false;
        }

        $found = false;
        foreach ($elements[$elementId]['option'] as $key => $option) {

            if ($option['value'] == $newOptionSlug || $option['name'] == $newOption) {
                $found = true;
                break;
            }
        }

        if (!$found && $newOptionSlug !== '') {
            $elements[$elementId]['option'][] = array(
                'name'  => $newOption,
                'value' => $newOptionSlug,
            );
            $type->config->set('elements', $elements);

            $type->bindElements($elements);
            $type->save();
            return true;
        }

        return false;
    }

    /**
     * @param        string
     * @param string $elementId
     * @param string $typeId
     * @param int    $appId
     * @return bool
     */
    public function checkOptionColor($newOption, $elementId, $typeId, $appId)
    {
        $application = $this->app->table->application->get($appId);
        if ($application) {
            $type = $application->getType($typeId);
        }

        if (!isset($type)) {
            return false;
        }

        $jbcolor  = $this->app->jbcolor;
        $elements = $type->config->get('elements', array());
        $oldItems = $elements[$elementId]['colors'];

        $colors = $jbcolor->parse($oldItems);

        if (strpos($newOption, '#')) {
            list($label, $color) = explode('#', $newOption);
        } else {
            $label = $newOption;
        }

        $label = JString::trim($label);

        if (empty($label)) {
            return false;
        }

        $newItems = $jbcolor->build($newOption, $colors);

        if ($oldItems == $newItems) {
            return false;

        } else if (!empty($newItems)) {

            $elements[$elementId]['colors'] = $newItems;

            $type->config->set('elements', $elements);
            $type->bindElements($elements);
            $type->save();

            return true;
        }

    }

    /**
     * @param $newOption
     * @param $elementId
     * @param $typeId
     * @param $appId
     * @return bool
     */
    public function checkOptionCascade($newOption, $elementId, $typeId, $appId)
    {
        $application = $this->app->table->application->get($appId);

        if ($application) {
            $type = $application->getType($typeId);
        }

        if (!isset($type)) {
            return false;
        }

        $elements = $type->config->get('elements', array());
        $options  = $this->app->jbselectcascade->getItemList(
            $elements[$elementId]['select_names'],
            $elements[$elementId]['items']
        );

        $oldItems = $elements[$elementId]['items'];

        $this->_setCascadeNewOptions($newOption, $options['items']);

        $newItems    = $this->_getCascadeOptionsAsString($options['items']);
        $stringItems = implode("\r\n", $newItems);

        if ($oldItems == $stringItems) {
            return false;

        } else if (!empty($stringItems)) {

            $elements[$elementId]['items'] = $stringItems;

            $type->config->set('elements', $elements);
            $type->bindElements($elements);
            $type->save();

            return true;
        }

        return false;
    }

    /**
     * @param     $options
     * @param     $items
     * @param int $level
     * @return bool
     */
    protected function _setCascadeNewOptions($options, & $items, $level = 0)
    {
        $options = array_values($options);

        if (!isset($options[$level])) {
            return false;
        }

        $current = $options[$level];

        if (!isset($items[$current])) {
            $items[$current] = array();
        }

        $this->_setCascadeNewOptions($options, $items[$current], ++$level);
    }


    /**
     * @param       $items
     * @param       $level
     * @param array $newItems
     * @return array
     */
    protected function _getCascadeOptionsAsString($items, $level = 0, $newItems = array())
    {
        $stringLevel = str_repeat('-', $level);
        $current     = array_keys($items);

        if ($current != 0) {
            $level++;
        }

        foreach ($current as $value) {
            if (!empty($value)) {
                $newItems[] = $stringLevel . $value;
            }

            $newItems = array_merge($newItems, $this->_getCascadeOptionsAsString($items[$value], $level));
        }

        return $newItems;
    }
}
