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
 * Class FilterPropsRenderer
 */
class FilterPropsRenderer extends AppRenderer
{

    protected $_type = null;
    protected $_template = null;
    protected $_application = null;
    protected $_config_file = 'positions.config';
    protected $_xml_file = 'positions.xml';
    protected $_moduleParams = null;

    /**
     * Render element
     * @param       $layout
     * @param array $args
     * @return null|string
     */
    public function render($layout, $args = array())
    {
        $this->_template    = $args['layout'];
        $this->_application = $args['application'];
        $this->_type        = $args['type'];

        $this->app->jbfilter->set($this->_type, $this->_application);

        $result = parent::render($layout, $args);

        return $result;
    }

    /**
     * Check position
     * @param string $position
     * @return bool
     */
    public function checkPosition($position)
    {
        foreach ($this->_getConfigPosition($position) as $data) {
            if (isset($data['element'])) {
                return true;
            }
        }

        return false;
    }

    /**
     * Render position
     * @param string $position
     * @param array  $args
     * @return string
     */
    public function renderPosition($position, $args = array())
    {
        // init vars
        $output = array();
        $i      = 0;

        // TODO check file exists
        $style          = (isset($args['style']) && $args['style']) ? $args['style'] : 'filter.block';
        $elementsConfig = $this->_getConfigPosition($position);

        foreach ($elementsConfig as $data) {

            $element = $this->app->jbfilter->getElement($data['element']);

            if ($element && $element->canAccess()) {
                $i++;
                $params = array_merge(
                    array(
                        'first'               => ($i == 1),
                        'last'                => ($i == count($elementsConfig) - 1),
                        'item_type'           => $this->_type,
                        'item_template'       => $this->_template,
                        'item_application_id' => $this->_application->id,
                        'moduleParams'        => $this->_moduleParams,
                    ),
                    $data,
                    $args
                );

                $value = $this->getElementRequest($element->identifier);

                $elementHTML = $this->app->jbfilterprops->elementRender($element->identifier, $value, $params);

                if (empty($elementHTML)) {
                    continue;
                }

                if ($style) {
                    $output[$i] = parent::render($style, array(
                            'element'     => $element,
                            'params'      => $params,
                            'value'       => $value,
                            'config'      => $element->getConfig(),
                            'elementHTML' => $elementHTML
                        )
                    );

                } else {
                    $output[$i] = $elementHTML;
                }
            }
        }

        return implode(PHP_EOL, $output);
    }

    /**
     * Get element request
     * @param $identifier
     * @return null|array|string
     */
    public function getElementRequest($identifier)
    {
        $elements = $this->app->jbrequest->get('e');
        if (is_array($elements)) {
            return (isset($elements[$identifier]) ? $elements[$identifier] : null);
        }

        return null;
    }

    /**
     * Get render config
     * @param $dir
     * @return array
     */
    public function getConfig($dir)
    {
        // config file
        if (empty($this->_config)) {
            if ($file = $this->_path->path('default:' . $dir . '/' . $this->_config_file)) {
                $content = $this->app->jbfile->read($file);
            } else {
                $content = null;
            }

            $this->_config = $this->app->parameter->create($content);
        }

        return $this->_config;
    }

    /**
     * Check path
     * @param $dir
     * @return bool
     */
    public function pathExists($dir)
    {
        return (bool)$this->_getPath($dir);
    }

    /**
     * Set Joomla module params
     * @param $params
     * @return $this
     */
    public function setModuleParams($params)
    {
        $this->_moduleParams = $params;
        return $this;
    }

    /**
     * Get config position
     * @param string $position
     * @return array
     */
    protected function _getConfigPosition($position)
    {
        $configName = $this->_application->getGroup() . '.' . $this->_type . '.' . $this->_template;
        $config     = $this->getConfig('item')->get($configName);

        return $config && isset($config[$position]) ? $config[$position] : array();
    }

}