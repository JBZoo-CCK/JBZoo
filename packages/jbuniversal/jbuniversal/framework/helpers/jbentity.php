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
 * Class JBEntityHelper
 */
class JBEntityHelper extends AppHelper
{
    /**
     * Elements cache
     * @var array
     */
    protected $_elements = array();

    /**
     * Item types cache
     * @var array
     */
    protected $_types = array();

    /**
     * Appplications cache
     * @var array
     */
    protected $_applications = array();

    /**
     * Class constructor
     * @param $app App
     */
    public function __construct($app)
    {
        parent::__construct($app);

        $this->app->loader->register('Type', 'classes:type.php');
        $this->app->loader->register('FilterElement', 'classes:/filter/element.php');
    }


    /**
     * Get element by id
     * @param string      $elementId
     * @param string|null $type
     * @param string|null $applicationId
     * @return mixed
     * @throws Exception
     */
    public function getElement($elementId, $type = null, $applicationId = null)
    {
        if (!isset($this->_elements[$elementId])) {
            $zooType                     = $this->getType($type, $applicationId);
            $this->_elements[$elementId] = $zooType->getElement($elementId);
        }

        if (isset($this->_elements[$elementId])) {
            return $this->_elements[$elementId];
        }

        throw new Exception('Unknow element.' . print_r(func_num_args(), true));
    }

    /**
     * Get type
     * @param string $type
     * @param int    $applicationId
     * @return Type
     */
    public function getType($type, $applicationId)
    {
        if (!isset($this->_types[$type])) {
            $application         = $this->getApplication($applicationId);
            $this->_types[$type] = new Type($type, $application);
        }

        return $this->_types[$type];
    }

    /**
     * Get application by ID
     * @param int $applicationId
     * @return Application
     */
    public function getApplication($applicationId)
    {
        $applicationId = (int)$applicationId;

        if (!isset($this->_applications[$applicationId])) {
            $this->_applications[$applicationId] = $this->app->table->application->get($applicationId);
        }

        return $this->_applications[$applicationId];
    }

    /**
     * Get element model
     * @param string  $elementId
     * @param string  $type
     * @param int     $applicationId
     * @param boolean $isRange
     * @return JBModelElement
     */
    public function getElementModel($elementId, $type, $applicationId, $isRange = false)
    {
        $element = $this->getElement($elementId, $type, $applicationId);
        if (!$element) {
            return null;
        }

        $elementType = strtolower(basename(get_class($element)));
        $elementType = str_replace('element', '', $elementType);

        $modelName = 'JBModelElement' . $elementType;

        if (class_exists($modelName)) {
            return new $modelName($element, $applicationId, $type);

        } elseif ($isRange && class_exists('JBModelElementRange')) {
            return new JBModelElementRange($element, $applicationId, $type);

        } elseif (!$isRange && class_exists('JBModelElement')) {
            return new JBModelElement($element, $applicationId, $type);

        } else {
            $this->app->error->raiseError(500, 'Model not found - ' . $modelName);
        }

        return null;
    }

    /**
     * Get all itemtypes data
     * @param bool $groupByType
     * @return array
     */
    public function getItemTypesData($groupByType = false)
    {
        static $result;

        $groupByType = (int)$groupByType ? 1 : 0;

        if (!isset($result[$groupByType])) {

            if (!is_array($result)) {
                $result = array();
            }

            $result[$groupByType] = array();

            $typesPath = $this->app->path->path('jbtypes:');
            $files     = JFolder::files($typesPath, '.config');

            $result[$groupByType] = array();
            foreach ($files as $file) {
                $fileContent = $this->app->jbfile->read($typesPath . '/' . $file);
                $typeData    = json_decode($fileContent, true);
                $typeAlias   = str_replace('.config', '', $file);

                if (isset($typeData['elements']) && !empty($typeData['elements'])) {
                    if ($groupByType) {
                        $result[$groupByType][$typeAlias] = $typeData['elements'];
                    } else {
                        $result[$groupByType] = array_merge($result[$groupByType], $typeData['elements']);
                    }
                }
            }
        }

        return $result[$groupByType];
    }

    /**
     * Get element type by it ID
     * @param string $elementId
     * @return null|string
     */
    public function getTypeByElementId($elementId)
    {
        $elements = $this->getItemTypesData(false);
        if (isset($elements[$elementId])) {
            return $elements[$elementId]['type'];
        }

        return null;
    }

    /**
     * Get element type by it ID
     * @param string $elementId
     * @return null|string
     */
    public function getItemTypeByElementId($elementId)
    {
        $elements = $this->getItemTypesData(true);

        foreach ($elements as $itemType => $elements) {
            if (isset($elements[$elementId])) {
                return $itemType;
            }
        }

        return null;
    }

    /**
     * Get field name by it
     * @param $fieldId
     * @return string
     */
    public function getFieldNameById($fieldId)
    {
        $elements = $this->getItemTypesData(false);

        if (isset($elements[$fieldId]['name'])) {
            return $elements[$fieldId]['name'];
        }

        return JText::_('JBZOO_FIELDS_CORE_' . $fieldId);
    }

}
