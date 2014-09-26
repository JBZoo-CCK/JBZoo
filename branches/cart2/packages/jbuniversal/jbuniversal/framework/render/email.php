<?php
/**
 * JBZoo App is universal Joomla CCK, application for YooTheme Zoo component
 *
 * @package     jbzoo
 * @version     2.x Pro
 * @author      JBZoo App http://jbzoo.com
 * @copyright   Copyright (C) JBZoo.com,  All rights reserved.
 * @license     http://jbzoo.com/license-pro.php JBZoo Licence
 * @coder       Alexander Oganov <t_tapak@yahoo.com>
 */

// no direct access
defined('_JEXEC') or die('Restricted access');


/**
 * Class EmailRenderer
 */
class EmailRenderer extends PositionRenderer
{
    /**
     * @var JBCartElementHelper
     */
    protected $_cartelement;
    /**
     * @var JBCartPositionHelper
     */
    protected $_position;

    /**
     * @var JBModelConfig
     */
    protected $_config;

    /**
     * @var JBCartOrder|Comment
     */
    protected $_subject;

    /**
     * @var array
     */
    protected $_systemPositions = array(
        self::POSITION_TITLE  => 'Title',
        self::POSITION_ATTACH => 'Attach'
    );

    const MAIN_GROUP      = 'cart.';
    const POSITION_ATTACH = 'attach';
    const POSITION_TITLE  = 'title';

    /**
     * Class constructor
     *
     * @param App  $app
     * @param null $path
     */
    public function __construct($app, $path = null)
    {
        parent::__construct($app, $path);

        $this->_position    = $app->jbcartposition;
        $this->_cartelement = $app->jbcartelement;
        $this->_config      = JBModelConfig::model();
    }

    /**
     * @param string $layout
     * @param array  $args
     *
     * @return string
     */
    public function render($layout, $args = array())
    {
        // set subject
        $this->_subject = isset($args['subject']) ? $args['subject'] : null;
        $this->_layout  = $layout;

        $result = '';

        // render layout
        $result .= parent::render(JBCart::ELEMENT_TYPE_EMAIL . '.' . $layout, $args);

        return $result;
    }

    /**
     * @param  string $position
     * @param  array  $args
     *
     * @return bool|string
     */
    public function renderPosition($position, $args = array())
    {
        // init vars
        $elements = array();
        $output   = array();
        $style    = isset($args['style']) ? 'order.email.' . $args['style'] : 'order.email.simple';
        $layout   = $this->_layout;

        // render elements
        foreach ($this->_getConfigPosition($position) as $index => $config) {

            if ($element = $this->_cartelement->create($config['type'], $config['group'], $config)) {

                //set subject
                $element->setSubject($this->_subject);
                //set config
                $element->setConfig($config);
                if (!$element->hasValue()) {
                    return false;
                }

                $args['_layout']   = $this->_layout;
                $args['_position'] = $position;
                $args['_index']    = $index;

                // set params
                $params = array_merge((array)$config, $args);

                $elements[] = compact('element', 'params');

            }
        }

        foreach ($elements as $i => $data) {

            $output[$i] = parent::render('element.' . $style, array(
                'element' => $data['element'],
                'params'  => array_merge(
                    array(
                        'first' => ($i == 0),
                        'last'  => ($i == count($elements) - 1)
                    ),
                    $data['params']
                ),
            ));
        }

        // restore layout
        $this->_layout = $layout;

        return implode("\n", $output);
    }

    /**
     * Check if user can access position
     *
     * @param  string $position
     *
     * @return bool
     */
    public function checkPosition($position)
    {
        foreach ($this->_getConfigPosition($position) as $config) {
            if ($element = $this->_cartelement->create($config['type'], $config['group'], $config)) {

                $element->setSubject($this->_subject);
                //$element->setConfig($config);
                if ($element->canAccess() && $element->hasValue()) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * @param $position
     *
     * @return mixed
     */
    public function _getConfigPosition($position)
    {
        $config   = $this->_getConfig();
        $position = $config->get($position);

        return isset($position) ? $position : array();
    }

    /**
     * @param string $dir
     *
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
     * Retrieve positions of a layout and add system positions if not exists - Title, Attach.
     *
     * @param string $dir point separated path to layout, last part is layout
     *
     * @return array The positions array
     * @since 2.0
     */
    public function getPositions($dir)
    {
        $positions = parent::getPositions($dir);

        $positions['positions'] = array_merge($positions['positions'], $this->_systemPositions);

        return $positions;
    }

    /**
     * @return array|bool
     */
    public function getAttach()
    {
        $files = array();
        $names = array();

        $attachments = array();
        foreach ($this->_getConfigPosition(self::POSITION_ATTACH) as $config) {
            if ($element = $this->_cartelement->create($config['type'], $config['group'])) {

                $element->setSubject($this->_subject);
                $element->setConfig($config);
                if (!$element->canAccess() && !$element->hasValue() && !$element->getOrder()) {
                    return false;
                }

                $file = $element->getFile();
                $name = $element->config->get('name') . ' - ' . basename($file);

                $files[] = $file;
                $names[] = $name;

                $attachments = compact('files', 'names');
            }
        }

        return $attachments;
    }

    /**
     * @param  string $__layout
     * @param  array  $__args
     *
     * @return string
     */
    public function partial($__layout, $__args = array())
    {
        // init vars
        if (is_array($__args)) {
            foreach ($__args as $__var => $__value) {
                $$__var = $__value;
            }
        }
        $layout = $this->_getLayout($__layout);

        // render layout
        $__html = '';
        ob_start();
        include($layout);
        $__html = ob_get_contents();
        ob_end_clean();

        return $__html;
    }

    /**
     * Build default attributes or merge with needed
     *
     * @param  array $attrs
     *
     * @return mixed
     */
    public function getAttrs($attrs = array())
    {
        $default = array(
            'align' => 'left',
        );

        if (empty($attrs)) {
            return $this->app->jbhtml->buildAttrs($default);
        }

        $merged = array_merge($default, $attrs);

        return $this->app->jbhtml->buildAttrs($merged);
    }

    /**
     * Build default styles or merge with needed
     *
     * @param  bool  $merge
     * @param  array $styles
     *
     * @return mixed
     */
    public function getStyles($styles = array(), $merge = false)
    {
        $default = array(
            'text-align'    => 'left',
            'border-bottom' => '1px solid #dddddd',
            'font-style'    => 'italic',
            'font-size'     => '12px',
            'color'         => '#000'
        );

        if (empty($styles)) {
            return $this->buildStyles($default);
        }

        if ($merge === true) {
            $styles = array_merge($default, $styles);
        }

        return $this->buildStyles($styles);
    }

    /**
     * Build styles from array
     *
     * @param  $styles
     *
     * @return string
     */
    public function buildStyles($styles)
    {
        $result = ' style="';

        if (is_string($styles)) {
            $result .= $styles;

        } elseif (!empty($styles)) {
            foreach ($styles as $key => $value) {

                if (!empty($value) || $value == '0' || $key == 'value') {
                    $result .= $key . ':' . $value . ';';
                }
            }
        }

        $result .= "\"";
        return JString::trim($result);
    }

    /**
     * @param         $text
     * @param  string $color - name|hex|rgb
     * @param  int    $size  - from 1 to 7
     *
     * @return string
     */
    public function fontColor($text, $color = '#000', $size = 2)
    {
        return '<i><font size="' . $size . '" color="' . $color . '">' . $text . '</font></i>';
    }

    /**
     * @return mixed
     */
    protected function _getConfig()
    {
        $params = $this->_config->getGroup(self::MAIN_GROUP . JBCart::CONFIG_EMAIL_TMPL . '.' . $this->_layout);

        return $params;
    }

    /**
     * @param null $layout
     *
     * @return string
     */
    protected function _getLayout($layout = null)
    {
        // set default
        if ($layout == null) {
            return false;
        }

        return $this->app->path->path("jbtmpl:catalog/renderer/email/{$this->_layout}/{$layout}{$this->_extension}");
    }

}