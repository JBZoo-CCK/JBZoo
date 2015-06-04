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
 * Class JBPriceRenderer
 */
class JBPriceRenderer extends PositionRenderer
{
    /**
     * Price element id.
     * @var string
     */
    protected $element_id;

    /**
     * Item layout name.
     * @var string
     */
    protected $itemLayout;

    /**
     * @var JBCartVariant
     */
    protected $_variant;

    /**
     * @type JBModelConfig
     */
    protected $_jbconfig;

    /**
     * @param App  $app
     * @param null $path
     */
    public function __construct($app, $path = null)
    {
        parent::__construct($app, $path);

        $this->_jbconfig = JBModelConfig::model();
    }

    /**
     * @param string $position
     * @return bool|void
     */
    public function checkPosition($position)
    {
        $_index = 0;
        foreach ($this->getConfigPosition($position) as $index => $data) {
            if ($element = $this->_variant->get($data['identifier'])) {
                // Backward Compatibility. Delete later.
                if (!is_numeric($index)) {
                    $index = $_index++;
                }
                $data['_layout']   = $this->_layout;
                $data['_position'] = $position;
                $data['_index']    = $index;

                if ($element->canAccess() && $element->hasValue(new AppData($data))) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * @param string $layout
     * @param array  $args
     * @return string
     */
    public function render($layout, $args = array())
    {
        $this->element_id = array_key_exists('element_id', $args) ? $args['element_id'] : null;
        $this->itemLayout = array_key_exists('layout', $args) ? $args['layout'] : null;

        $this->_variant = array_key_exists('_variant', $args) ? $args['_variant'] : null;

        $result = parent::render('jbprice.' . $layout, $args);

        return $result;
    }

    /**
     * @param string $position
     * @param array  $args
     * @return string|void
     */
    public function renderPosition($position, $args = array())
    {

        // init vars
        $elements = array();
        $output   = array();
        $_index   = 0;

        // get style
        $style = isset($args['style']) ? 'jbprice.' . $args['style'] : 'jbprice.default';

        // store layout
        $layout = $this->_layout;
        foreach ($this->getConfigPosition($position) as $index => $data) {

            if ($element = $this->_variant->get($data['identifier'])) {
                if (!$element->canAccess()) {
                    continue;
                }
                // Backward Compatibility. Delete later.
                if (!is_numeric($index)) {
                    $index = $_index++;
                }

                $data['_price_layout'] = $this->itemLayout;

                $data['_layout']   = $this->_layout;
                $data['_position'] = $position;
                $data['_index']    = $index;

                // set params
                $params   = array_merge($data, $args);
                $hasValue = $element->hasValue(new AppData($params));
                if (!$hasValue && $element->isCore()) {
                    $hasValue = $params['_isEmpty'] = true;
                }

                if ($hasValue) {
                    $elements[] = compact('element', 'params');
                }
            }
        }
        $count = count($elements);
        foreach ($elements as $i => $data) {
            $params = array_merge(array('first' => ($i === 0), 'last' => ($i === $count - 1)), $data['params']);
            $data['element']->setIndex($params['_index'])->setPosition($params['_position'])->loadAssets();

            $output[$i] = parent::render('element.' . $style, array(
                'element' => $data['element'],
                'params'  => new AppData($params)
            ));
        }
        $this->_layout = $layout;

        return implode(PHP_EOL, $output);
    }

    /**
     * @param array $args
     * @return string
     */
    public function renderEditPositions($args = array())
    {
        // init vars
        $elements = array();
        $output   = array();

        // get style
        $style  = $this->_variant->isBasic() ? 'jbprice._basic' : 'jbprice._variations';
        $config = $this->_getConfig();

        if (!empty($config)) {
            foreach ($config as $key => $data) {
                if (($element = $this->_variant->get($key))
                ) {
                    if ($this->_variant->isBasic() && !$element->isCore()) {
                        continue;
                    }

                    $data['_price_layout'] = $this->itemLayout;

                    $data['_layout'] = $this->_layout;
                    $data['_index']  = $key;

                    // set params
                    $params = array_merge($data, $args);

                    $elements[] = compact('element', 'params');
                }
            }
        }

        $count = count($elements);
        if ($count) {
            foreach ($elements as $i => $data) {
                $params = array_merge(array('first' => ($i == 0), 'last' => ($i == $count - 1)), $data['params']);

                $data['element']->loadEditAssets();
                $output[$i] = parent::render('element.' . $style, array(
                    'element' => $data['element'],
                    'params'  => new AppData($params)
                ));
            }
        }

        return implode(PHP_EOL, $output);
    }

    /**
     * @param $position
     * @return mixed
     */
    public function getConfigPosition($position)
    {
        $config   = $this->_getPositions();
        $position = (array)$config->get($this->element_id . '.' . $this->_layout . '.' . $position, array());

        return $position;
    }

    /**
     * @param string $dir
     * @return array
     */
    public function getLayouts($dir)
    {
        // init vars
        $layoutList = array();
        $parts      = explode('.', $dir);
        $path       = implode('/', $parts);

        // parse positions xml
        if ($xml = simplexml_load_file($this->_getPath($path . '/' . $this->_xml_file))) {

            $layouts = $xml->xpath('positions[@layout]');

            foreach ($layouts as $layout) {
                $name = (string)$layout->attributes()->layout;

                $layoutList[$name] = $name;
            }

        }

        return $layoutList;
    }

    /**
     * @return AppData
     */
    public function _getPositions()
    {
        return $this->_jbconfig->getGroup('cart.' . JBCart::CONFIG_PRICE_TMPL);
    }

    /**
     * @return mixed
     */
    protected function _getConfig()
    {
        return $this->_jbconfig->getGroup('cart.' . JBCart::CONFIG_PRICE . '.' . $this->element_id)->get(JBCart::DEFAULT_POSITION);
    }
}
