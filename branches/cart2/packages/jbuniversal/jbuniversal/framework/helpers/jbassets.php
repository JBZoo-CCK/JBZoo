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
 * Class JBAssetsHelper
 */
class JBAssetsHelper extends AppHelper
{
    const GROUP_CORE    = 'core';
    const GROUP_LIBRARY = 'library';
    const GROUP_DEFAULT = 'default';

    /**
     * @type array
     */
    protected $_list = array(
        'js'  => array(
            self::GROUP_CORE    => array(),
            self::GROUP_LIBRARY => array(),
            self::GROUP_DEFAULT => array(),
        ),
        'css' => array(
            self::GROUP_CORE    => array(),
            self::GROUP_LIBRARY => array(),
            self::GROUP_DEFAULT => array(),
        ),
    );

    /**
     * Mapping: 'jQuery Widget Name' => 'helper action'
     * Experimental!
     * @type array
     */
    protected $_libraryMap = array(
        'jbzoocart'   => 'basket',
        'jbzoocolors' => 'colors',
        'fancybox'    => array(
            'jQuery',
            'fancybox',
        ),
    );

    /**
     * @type bool
     */
    protected $_isAjax = false;

    /**
     * @param App $app
     */
    public function __construct($app)
    {
        parent::__construct($app);

        $this->_isAjax = !(empty($files) &&
            $this->app->jbrequest->isAjax() &&
            $this->app->jbrequest->is('format', 'feed') &&
            $this->app->jbrequest->is('format', 'raw'));
    }

    /**
     * @param string $jquerySelector
     * @param string $widgetName
     * @param array  $params
     * @param bool   $return
     * @param bool   $isComplex
     * @return string
     */
    public function widget($jquerySelector, $widgetName, $params = array(), $return = false, $isComplex = false)
    {
        static $included = array();

        $jquerySelector = is_array($jquerySelector) ? implode(', ', $jquerySelector) : $jquerySelector;

        $hash = implode('///', array($jquerySelector, $widgetName, (int)$return, (int)$isComplex));

        if (!isset($included[$hash])) {
            $included[$hash] = true;

            $this->tools();

            $widgetName = str_replace('.', '', $widgetName);

            // experimental lib loader!
            $mapName = $this->app->jbvars->lower($widgetName, true);
            if (isset($this->_libraryMap[$mapName])) {
                $methods = (array)$this->_libraryMap[$mapName];
                foreach ($methods as $method) {
                    call_user_func(array($this, $method));
                }
            }

            // widget init script
            $initScript = '$("' . $jquerySelector . '").' . $widgetName . '(' . $this->toJSON($params) . ', ' . (int)$isComplex . ');';

            if ($return) {
                return implode(PHP_EOL, array(
                    '<script type="text/javascript">',
                    "\tjQuery(function($){ setTimeout(function(){" . $initScript . "}, 0); });",
                    '</script>',
                ));
            }

            $this->addScript($initScript);
        }

        return null;
    }

    /**
     * Include JS in document
     * @param array  $files
     * @param string $group
     * @return bool
     */
    public function js($files, $group = self::GROUP_DEFAULT)
    {
        return $this->_include((array)$files, 'js', $group);
    }

    /**
     * Include CSS in document
     * @param array  $files
     * @param string $group
     * @return bool
     */
    public function css($files, $group = self::GROUP_DEFAULT)
    {
        return $this->_include((array)$files, 'css', $group);
    }

    /**
     * Complile&cache less and include rsult to document
     * @param        $files
     * @param string $group
     * @return bool
     */
    public function less($files, $group = self::GROUP_DEFAULT)
    {
        if (!$this->_isAjax) {
            return false;
        }

        $files = (array)$files;

        $resultFiles = array();
        foreach ($files as $file) {
            $resultFiles[] = $this->app->jbless->compile($file);
        }

        return $this->_include($resultFiles, 'css', $group);
    }

    /**
     * @return array
     */
    public function getList()
    {
        return $this->_list;
    }

    /**
     * @param $vars
     * @return mixed|string
     */
    public function toJSON($vars)
    {
        //$vars = $this->app->jbarray->cleanJson($vars);
        if (is_object($vars)) { // for scalar vars
            $vars = (array)$vars;
        }

        if (!empty($vars)) {
            return json_encode($vars);
        }

        return '{}';
    }

    /**
     * Set application styles files
     * @param string $alias
     */
    public function setAppCss($alias = null)
    {
        $this->css('jbassets:css/jbzoo.' . $alias . '.css');
        $this->less('jbassets:less/jbzoo.' . $alias . '.less');
    }

    /**
     * Add script and styles for back-end
     */
    public function admin()
    {
        if ($this->app->jbenv->isSite()) {
            return;
        }

        $jVersion = $this->app->jbversion->joomla('2.7.0') ? '3' : '2';

        $this->tools();
        $this->less('jbassets:less/general.less');
        $this->less('jbassets:less/admin.less');
        $this->less('jbassets:less/admin/joomla-' . $jVersion . '.less');

        $this->js(array(
            'jbassets:js/admin/colors.js',
            'jbassets:js/admin/delimiter.js',
            'jbassets:js/admin/editpositions.js',
            'jbassets:js/admin/ordermacroslist.js',
            'jbassets:js/admin/itemorder.js',
            'jbassets:js/admin/keyvalue.js',
            'jbassets:js/admin/jkeyvalue.js',
            'jbassets:js/admin/menu.js',
            'jbassets:js/back-end.js',
        ));

        $this->addVar('joomlaVersion', $jVersion);
    }

    /**
     * Set application JavaScript files
     * @param string $alias
     */
    public function setAppJS($alias = null)
    {
        $this->tools();
        $this->js('jbassets:js/jbzoo.' . $alias . '.js');
    }

    /**
     * Include JBZoo Tools script
     */
    public function tools()
    {
        static $isAdded = false;

        if (!$isAdded) {
            $isAdded = true;

            $this->jQuery();
            $this->addScript(implode("\n\t", array(
                'JBZoo.DEBUG = ' . (int)JDEBUG . ';',
                'jQuery.migrateMute = false;',
            )), false);

            $this->js(array(
                'jbassets:js/libs/browser.min.js', // for compatible with old jQuery plugins
                'jbassets:js/libs/cookie.min.js',
                'jbassets:js/libs/sweet-alert.min.js',
                'jbassets:js/helper.js',
                'jbassets:js/widget.js',
                'jbassets:js/jbzoo.js',
            ), self::GROUP_LIBRARY);

            $this->js(array(
                'jbassets:js/widget/goto.js',
                'jbassets:js/widget/select.js',
                'jbassets:js/widget/money.js',
                'jbassets:js/widget/heightfix.js',
            ));

            $this->css(array('jbassets:css/libs/sweet-alert.css'), self::GROUP_LIBRARY);

            if ($this->app->jbenv->isSite()) {
                $cartItems = JBCart::getInstance()->getItems();
                $this->addVar('currencyList', $this->app->jbmoney->getData());
                $this->addVar('cartItems', $this->app->jbarray->map($cartItems, 'element_id', 'element_id', 'item_id'));
            }

            $this->addVar('JBZOO_DIALOGBOX_OK', 'Ok');
            $this->addVar('JBZOO_DIALOGBOX_CANCEL', 'Cancel');

            $this->widget('.jbzoo .jsGoto', 'JBZoo.Goto');
            $this->widget('.jbzoo select', 'JBZoo.Select');
        }
    }

    /**
     * Include UIkit files
     * @param bool $addJS
     * @param bool $isGradient
     */
    public function uikit($addJS = false, $isGradient = false)
    {
        if ($addJS) {
            $this->js('jbassets:js/libs/uikit.min.js', self::GROUP_CORE);
        }

        if ($isGradient) {
            $this->css('jbassets:css/uikit.gradient.min.css', self::GROUP_CORE);
        } else {
            $this->css('jbassets:css/uikit.min.css', self::GROUP_CORE);
        }
    }

    /**
     * @param string $id
     * @param array  $params
     * @param bool   $return
     * @return string
     */
    public function currencyToggle($id, $params = array(), $return = false)
    {
        $this->less('jbassets:less/widget/currencytoggle.less');
        $this->js(array(
            'jbassets:js/widget/money.js',
            'jbassets:js/widget/currencytoggle.js',
        ));

        $script = $this->widget('#' . $id, 'JBZoo.CurrencyToggle', $params, $return);
        if ($return && $script) {
            return $script;
        }
    }

    /**
     * Init filter assets
     */
    public function filter()
    {
        $this->tools();
        $this->css('mod_jbzoo_search:assets/less/filter.less');
    }

    /**
     * Include jQuery UI lib
     */
    public function jQueryUI()
    {
        static $isAdded = false;

        $this->jQuery();

        if (!$isAdded) {
            $isAdded = true;

            if ($this->app->jbenv->isSite()) {
                $this->css('libraries:jquery/jquery-ui.custom.css', self::GROUP_CORE);
                $this->js('libraries:jquery/jquery-ui.custom.min.js', self::GROUP_CORE);
            } else {
                $this->app->document->addScript('libraries:jquery/jquery-ui.custom.min.js');
                $this->app->document->addStylesheet('libraries:jquery/jquery-ui.custom.css');
            }
        }
    }

    /**
     * Include fancybox lib
     */
    public function fancybox()
    {
        $this->jQuery();

        $this->css(array(
            'jbassets:css/libs/fancybox.css',
        ), self::GROUP_LIBRARY);

        $this->js(array(
            'jbassets:js/libs/fancybox/core.min.js',
            'jbassets:js/libs/fancybox/buttons.min.js',
            'jbassets:js/libs/fancybox/media.min.js',
            'jbassets:js/libs/fancybox/thumbnail.min.js',
        ), self::GROUP_LIBRARY);
    }

    /**
     * Include table sorter lib
     */
    public function tablesorter()
    {
        $this->jQuery();
        $this->css('jbassets:css/libs/tablesorter.css', self::GROUP_LIBRARY);
        $this->js('jbassets:js/libs/tablesorter.min.js', self::GROUP_LIBRARY);
    }

    /**
     * Include chosen lib
     */
    public function chosen()
    {
        $this->jQuery();
        $this->css('jbassets:css/libs/chosen.css', self::GROUP_LIBRARY);
        $this->js('jbassets:js/libs/chosen.min.js', self::GROUP_LIBRARY);
    }

    /**
     * Include datepicker lib
     */
    public function datepicker()
    {
        $this->jQueryUI();
        $this->css('libraries:jquery/plugins/timepicker/timepicker.css', self::GROUP_CORE);
        $this->js('libraries:jquery/plugins/timepicker/timepicker.js', self::GROUP_CORE);
    }

    /**
     * Include slider js
     * @param string $id
     * @param array  $params
     * @param bool   $return
     * @return string
     */
    public function slider($id, $params = array(), $return = false)
    {
        $this->tools();
        $this->jqueryui();
        $this->js('jbassets:js/widget/slider.js');
        $this->less('jbassets:less/widget/slider.less');

        $script = $this->widget('#' . $id, 'JBZoo.Slider', $params, $return);
        if ($return && $script) {
            return $script;
        }
    }

    /**
     * Include datepicker lib
     */
    public function nivoslider()
    {
        $this->jQuery();
        $this->css('jbassets:css/libs/nivolider.css', self::GROUP_LIBRARY);
        $this->js('jbassets:js/libs/nivoslider.min.js', self::GROUP_LIBRARY);
    }

    /**
     * Include jQuery framework
     */
    public function jQuery()
    {
        static $isAdded = false;

        if (!$isAdded) {
            $isAdded = true;
            if (!$this->app->joomla->version->isCompatible('3.0')) {
                if (!$this->app->system->application->get('jquery')) {
                    $this->app->system->application->set('jquery', true);
                    $this->app->system->document->addScript($this->app->path->url('libraries:jquery/jquery.js'));
                }
            } else {
                JHtml::_('jquery.framework');
            }
        }
    }

    /**
     * Include basket script
     */
    public function basket()
    {
        $this->tools();
        $this->quantity();

        $this->less(array(
            'jbassets:less/cart/cart.less',
        ));

        $this->js(array(
            'jbassets:js/cart/cart.js',
            'jbassets:js/cart/module.js',
            'jbassets:js/cart/shipping.js',
            'jbassets:js/cart/shipping-type.js',
            'jbassets:js/widget/money.js',
        ));
    }

    /**
     * Include scripts and styles for compare
     */
    public function compare()
    {
        $this->tools();
        $this->js(array(
            'elements:jbcompare/assets/js/compare-buttons.js',
            'elements:jbcompare/assets/js/compare-table.js',
        ));
        $this->less('elements:jbcompare/assets/less/compare.less');
    }

    /**
     * Include progress bar in document
     */
    public function progressBar()
    {
        $this->jQueryUI();
        $this->tools();
        $this->js('jbassets:js/widget/progressbar.js');
    }

    /**
     * Include tabs widget in document
     */
    public function tabs()
    {
        $this->tools();
        $this->js('jbassets:js/widget/tabs.js');
        $this->less('jbassets:less/widget/tabs.less');
    }

    /**
     * Include tabs widget in document
     */
    public function accordion()
    {
        $this->tools();
        $this->js('jbassets:js/widget/accordion.js');
        $this->less('jbassets:less/widget/tabs.less');
    }

    /**
     * Load jquery calendar script
     * @depricated
     */
    public function calendar()
    {
        $this->datepicker();
    }

    /**
     * Include jQuery favorite
     */
    public function favorite()
    {
        $this->tools();
        $this->js(array(
            'elements:jbfavorite/assets/js/favorite-buttons.js',
            'elements:jbfavorite/assets/js/favorite-list.js',
        ));
        $this->less('elements:jbfavorite/assets/less/favorite.less');
    }

    /**
     * Load widget JBColors
     */
    public function colors()
    {
        $this->tools();
        $this->js('jbassets:js/widget/colors.js');
        $this->less('jbassets:less/widget/colors.less');

        return $this;
    }

    /**
     * Load quantity widget
     */
    public function quantity()
    {
        $this->tools();
        $this->less('jbassets:less/widget/quantity.less');
        $this->js('jbassets:js/widget/quantity.js');

        return $this;
    }

    /**
     * Load media widget
     */
    public function media()
    {
        $this->tools();
        $this->js('jbassets:js/widget/media.js');

        return $this;
    }

    /**
     * Init quantity widget
     * @param      $id
     * @param      $options
     * @param bool $return
     * @return string|void
     */
    public function initQuantity($id, $options, $return = false)
    {
        $this->tools();
        $this->quantity();
        $script = $this->widget('#' . $id, 'JBZoo.Quantity', $options, $return);
        if ($return && $script) {
            return $script;
        }
    }

    /**
     * Assets for payment page
     */
    public function payment()
    {
        $this->js('jbassets:js/cart/payment.js');
    }

    /**
     * Init jqueryui autocomplete
     */
    public function jbimagePopup()
    {
        $this->widget(
            array(
                'a.jbimage-link[rel=jbimage-popup]',
                'a.jbimage-gallery',
            ),
            'fancybox',
            array(
                'helpers' => array(
                    'title'   => array('type' => 'outside'),
                    'buttons' => array('position' => 'top'),
                    'thumbs'  => array('width' => 80, 'height' => 80),
                    'overlay' => array('locked' => false),
                ),
            )
        );
    }

    /**
     * Height fix for items columns
     * @param string $element
     */
    public function heightFix($element = '.column')
    {
        $this->tools();

        $jsQuery = array(
            '.jbzoo .items',
            '.jbzoo .subcategories',
            '.jbzoo .jbcart-payment',
        );

        $this->widget(implode(', ', $jsQuery), 'JBZoo.HeightFix', array('element' => $element));
    }

    /**
     * Add to script
     */
    public function addRootUrl()
    {
        $this->addVar('rootUrl', JURI::root());
    }

    /**
     * Include basketitems element widget
     */
    public function basketItems()
    {
        $this->tools();
    }

    /**
     * Add global variable to javascript
     * @param $varName
     * @param $value
     */
    public function addVar($varName, $value)
    {
        static $vars = array();

        if (!isset($vars[$varName])) {
            $vars[$varName] = true;
            $this->addScript("\n\tJBZoo.addVar(\"" . $varName . "\", " . $this->toJSON($value) . " )", false);
        }
    }

    /**
     * @param $varName
     * @param $value
     * @return string
     */
    public function mergeVar($varName, $value)
    {
        return "<script type=\"text/javascript\">JBZoo.mergeVar(\"" . $varName . "\", " . $this->toJSON($value) . ")</script>";
    }

    /**
     * Init select cascade
     */
    public function selectCascade()
    {
        $this->jQuery();
        $this->js(array(
            'jbassets:js/widget/select.js', // parent class
            'jbassets:js/widget/select-cascade.js',
        ));

        $this->less('jbassets:less/widget/cascade.less');
    }

    /**
     * Init jqueryui autocomplete
     */
    public function initAutocomplete()
    {
        static $isAdded = false;

        $this->jQuery();
        $this->jQueryUI();

        if (!$isAdded) {
            $isAdded = true;
            $this->addScript('$(".jbzoo .jsAutocomplete").each(function (n, obj) {
                var $input = $(obj),
                    $form = $input.closest("form");

                $input.autocomplete({
                    minLength: 2,
                    source: function( request, response ) {
                        var term = request.term;
                        lastXhr = $.getJSON("' . $this->app->jbrouter->autocomplete() . '", {
                                "name"  : $input.attr("name"),
                                "value" : term,
                                "app_id": $(".jsApplicationId", $form).val(),
                                "type"  : $(".jsItemType", $form).val()
                            }, function(data, status, xhr) {
                                $input.removeClass("ui-autocomplete-loading");
                                response(data);
                            }
                        );
                    }
                });
            })');
        }
    }

    /**
     * Init jqueryUI autoComplete
     */
    public function initPriceAutoComplete()
    {
        static $isAdded = false;

        $this->jQuery();
        $this->jQueryUI();

        if (!$isAdded) {
            $isAdded = true;
            $this->addScript('$(".jbzoo .jsPriceAutoComplete").each(function (n, obj) {
                var $input = $(obj),
                    $form = $input.closest("form"),
                widget = $input.autocomplete({
                    minLength: 2,
                    source: function( request, response ) {
                        var term = request.term;
                        lastXhr = $.getJSON("' . $this->app->jbrouter->autocomplete() . '",
                            {
                                "name"  : $input.attr("name"),
                                "value" : term,
                                "app_id": $(".jsApplicationId", $form).val(),
                                "type"  : $(".jsItemType", $form).val()
                            },
                            function(data, status, xhr) {

                                $input.removeClass("ui-autocomplete-loading");
                                response(data);
                            }
                        );
                    },
                    change: function(event, ui) {
                    },
                    select: function(event, ui) {
                    }
                });
            })');
        }
    }

    /**
     * jQuery accordion lib init
     */
    public function jqueryAccordion()
    {
        static $isAdded = false;

        $this->accordion();

        if (!$isAdded) {
            $isAdded = true;
            $this->addScript('$(".jbzoo .jsAccordion").each(function(n, obj){
                var $obj = $(obj),
                    id   = "jbaccordion-" + n;
                $obj.attr("id", id);
                $("#" + id).JBZooAccordion();
            })');
        }
    }

    /**
     * Init color widget
     * @param string  $queryElement
     * @param boolean $type
     * @param bool    $return
     * @return string
     */
    public function initJBColorHelper($queryElement, $type = true, $return = true)
    {
        // force include for back-end. Do not delete!
        $this->less('jbassets:less/general.less');
        $this->colors();

        if ($queryElement) {
            $script = $this->widget('#' . $queryElement, 'JBZoo.Colors', array('multiple' => (boolean)$type), $return);

            if ($return) {
                return $script;
            }
        }
    }

    /**
     * Init color widget
     * @param string $queryElement
     * @param string $text
     */
    public function initJBColorElement($queryElement, $text = null)
    {
        $this->jQuery();

        if ($queryElement) {
            $this->css('media:jui/css/jquery.minicolors.css', self::GROUP_CORE);
            $this->js('media:jui/js/jquery.minicolors.min.js', self::GROUP_CORE);
            $this->widget($queryElement, 'JBColorElement', array('message' => $text));
        }
    }

    /**
     * Init tooltip
     */
    public function initTooltip()
    {
        $this->jQueryUI();
        $this->widget(".jbzoo .jbtooltip", 'tooltip');
    }

    /**
     * @param      $queryElement
     * @param null $version
     */
    public function initJBDelimiter($queryElement, $version = null)
    {
        $this->jQuery();

        if (empty($version)) {
            $version = JString::substr($this->app->jbversion->joomla(), 0, 1);
        }

        $this->widget($queryElement, 'JBZoo.Delimiter', array('version' => $version));
    }

    /**
     * Add script to document
     * @param      $script
     * @param bool $docReady
     */
    public function addScript($script, $docReady = true)
    {
        if (!$this->app->jbrequest->isAjax()) {

            $script = trim(trim($script), ';') . ';';

            if ($docReady) {
                $script = "\tjQuery(function($){ " . $script . " });\n";
            } else {
                $script = "\t" . $script . "\n";
            }

            JFactory::getDocument()->addScriptDeclaration($script);
        }
    }

    /**
     * Get site root URL
     * @return string
     */
    public function _getRoot()
    {
        static $root;
        if (!isset($root)) {
            //$root = JUri::root();
            $root = '/';
        }

        return $root;
    }

    /**
     * Include files to document
     * @param array  $files
     * @param string $type
     * @param string $group
     * @return bool
     */
    protected function _include(array $files, $type, $group = self::GROUP_DEFAULT)
    {
        if (!$this->_isAjax) {
            return false;
        }

        foreach ($files as $origPath) {

            if (!$origPath || isset($this->_list[$type][$group][$origPath])) {
                continue;
            }

            $resultPath = $origPath;

            if (strpos($origPath, 'http') !== false) { // external path
                $resultPath = $origPath;

            } elseif (strpos($origPath, ':') > 0) { // virtual path

                $resultPath = null;
                if ($fullPath = $this->app->path->path($origPath)) {
                    $resultPath = $this->app->path->relative($fullPath);
                }

            }

            if ($resultPath) {
                $this->_list[$type][$group][$origPath] = $resultPath;
            }
        }

        return true;
    }

    /**
     * Init modal window
     * @param string $class
     * @param array  $opt
     */
    public function behaviorModal($class = 'modal', $opt = array())
    {
        JHTML::_('behavior.modal', 'a.' . $class, $opt);
    }

    /**
     * Add attr link target
     */
    public function jbzooLinks()
    {
        static $isAdded = false;

        if (!$isAdded) {
            $isAdded = true;
            $this->addScript('$(".jbzoo a").attr("target", "_top")');
        }
    }

    /**
     * @param string $relativePath
     * @param string $type
     * @param string $group
     * @return bool
     */
    public function includeFile($relativePath, $type = 'css', $group = self::GROUP_DEFAULT)
    {
        if (strpos($relativePath, 'http') === false) { // if not external and not core group

            $fullPath = JPATH_ROOT . '/' . $relativePath;

            if (JFile::exists($fullPath)) {
                $relativePath = JUri::root() . $relativePath;
                $relativePath = $relativePath . '?' . substr(filemtime($fullPath), -3); // no browser cache

            } else {
                return false;
            }
        }

        if ($type == 'css') {
            JFactory::getDocument()->addStylesheet($relativePath);
        } elseif ($type == 'js') {
            JFactory::getDocument()->addScript($relativePath);
        }

        return true;
    }

    /**
     * Load all assets files (js and css)
     */
    public function loadAll()
    {
        $assetsConfig = JBModelConfig::model()->getGroup('config.assets');
        $splitMode    = $assetsConfig->get('split_mode', 'group');
        $jbminifier   = $this->app->jbminifier;

        foreach ($this->_list as $type => $groupList) {
            $allFiles = array();

            foreach ($groupList as $group => $files) {

                if ($splitMode == 'none' || $group == self::GROUP_CORE) {

                    foreach ($files as $file) {
                        $this->includeFile($file, $type, $group);
                    }

                } else if ($splitMode == 'group') {
                    $this->includeFile($jbminifier->split($files, $type, $group), $type, $group);

                } else if ($splitMode == 'all') {
                    $allFiles = array_merge($allFiles, $files);
                }

            }

            if ($splitMode == 'all') {
                $this->includeFile($jbminifier->split($allFiles, $type, 'all'), $type, $group);
            }
        }
    }
}
