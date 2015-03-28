<?php
/**
 * JBZoo App is universal Joomla CCK, application for YooTheme Zoo component
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
    const POSITION_ATTACH = 'attachments';

    /**
     * @type JMail
     */
    protected $_mailer;

    /**
     * @type JBCartElementHelper
     */
    protected $_element;

    /**
     * @type JBCartPositionHelper
     */
    protected $_position;

    /**
     * @type JBModelConfig
     */
    protected $_config;

    /**
     * @type JBCartOrder
     */
    protected $_order;

    /**
     * Class constructor
     * @param App  $app
     * @param null $path
     */
    public function __construct($app, $path = null)
    {
        parent::__construct($app, $path);

        $this->_position = $app->jbcartposition;
        $this->_element  = $app->jbcartelement;
        $this->_config   = JBModelConfig::model();
    }

    /**
     * @param string $layout
     * @param array  $args
     * @return string
     */
    public function render($layout, $args = array())
    {
        // set subject
        $this->_order  = isset($args['order']) ? $args['order'] : null;
        $this->_mailer = isset($args['mailer']) ? $args['mailer'] : null;
        $this->_layout = $layout;

        // render layout
        $result = parent::render(JBCart::ELEMENT_TYPE_EMAIL . '.' . $layout, $args);

        $this->renderPosition(self::POSITION_ATTACH);

        return $result;
    }

    /**
     * Check if user can access position
     * @param  string $position
     * @return bool
     */
    public function checkPosition($position)
    {
        foreach ($this->_getConfigPosition($position) as $index => $config) {
            if ($element = $this->_element->create($config['type'], $config['group'], $config)) {

                //set config
                $element->setConfig($config);
                $element->setOrder($this->_order);
                $element->setMailer($this->_mailer);

                // set params
                $args['_layout']   = $this->_layout;
                $args['_position'] = $position;
                $args['_index']    = $index;

                $params = array_merge((array)$config, $args);

                $params = $this->app->data->create($params);
                if ($element->canAccess() && $element->hasValue($params)) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * @param  string $position
     * @param  array  $args
     * @return bool|string
     */
    public function renderPosition($position, $args = array())
    {
        // init vars
        $elements = array();
        $output   = array();
        $style    = isset($args['style']) ? 'email.' . $args['style'] : 'email.default';
        $layout   = $this->_layout;

        // render elements
        foreach ($this->_getConfigPosition($position) as $index => $config) {
            if ($element = $this->_element->create($config['type'], $config['group'], $config)) {

                $element->setConfig($config);
                $element->setOrder($this->_order);
                $element->setMailer($this->_mailer);

                $args['_layout']   = $this->_layout;
                $args['_position'] = $position;
                $args['_index']    = $index;

                // set params
                $params = array_merge((array)$config, $args);

                $params = $this->app->data->create($params);
                if (!$element->canAccess() || !$element->hasValue()) {
                    continue;
                }

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
                    (array)$data['params']
                ),
            ));

        }

        // restore layout
        $this->_layout = $layout;

        return implode(PHP_EOL, $output);
    }

    /**
     * @param $position
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
     * @param string $dir point separated path to layout, last part is layout
     * @return array The positions array
     * @since 2.0
     */
    public function getPositions($dir)
    {
        $positions = parent::getPositions($dir);

        $positions['positions'] = array_merge($positions['positions'], array(
            self::POSITION_ATTACH => 'Attachments'
        ));

        return $positions;
    }

    /**
     * @return $this|bool
     */
    public function addAttachment()
    {
        foreach ($this->_getConfigPosition(self::POSITION_ATTACH) as $index => $config) {
            if ($element = $this->_element->create($config['type'], $config['group'], $config)) {

                $element->setConfig($config);
                $element->setOrder($this->_order);

                $element->setMailer($this->_mailer);

                $args['_layout']   = $this->_layout;
                $args['_position'] = self::POSITION_ATTACH;
                $args['_index']    = $index;

                // set params
                $params = array_merge((array)$config, $args);

                $params = $this->app->data->create($params);
                if (method_exists($element, 'addAttachment') && $element->hasValue($params)) {
                    $element->addAttachment();
                }
            }
        }

        return $this;
    }

    /**
     * @param  string $__layout
     * @param  array  $__args
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
        ob_start();
        include($layout);
        $__html = ob_get_contents();
        ob_end_clean();

        return $__html;
    }

    /**
     * Build default attributes or merge with needed
     * @param  array $attrs
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
     * @param  bool  $merge
     * @param  array $styles
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
     * @param  $styles
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
        $params = $this->_config->getGroup('cart.' . JBCart::CONFIG_EMAIL_TMPL . '.' . $this->_layout);

        return $params;
    }

    /**
     * @param null $layout
     * @return string
     */
    protected function _getLayout($layout = null)
    {
        if (empty($layout)) {
            return false;
        }

        $name    = $this->app->zoo->getApplication()->name;
        $catalog = $this->app->path->path("jbtmpl:" . $name . "/renderer/email/{$this->_layout}/{$layout}.php");
        $system  = $this->app->path->path("jbapp:templates-system/renderer/email/{$this->_layout}/{$layout}.php");

        return !empty($catalog) ? $catalog : $system;
    }

}