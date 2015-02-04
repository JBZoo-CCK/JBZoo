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
    /**
     * Set application styles files
     * @param string $alias
     */
    public function setAppCss($alias = null)
    {
        $this->css('jbassets:css/jbzoo.' . $alias . '.css');
    }

    /**
     * Add script and styles for back-end
     */
    public function admin()
    {
        if ($this->app->jbenv->isSite()) {
            return;
        }

        $this->tools();
        $this->less('jbassets:less/general.less');
        $this->css('jbassets:css/admin.css');
        $this->js(array(
            'jbassets:js/admin/colors.js',
            'jbassets:js/admin/delimiter.js',
            'jbassets:js/admin/editpositions.js',
            'jbassets:js/admin/itemorder.js',
            'jbassets:js/admin/keyvalue.js',
            'jbassets:js/admin/jkeyvalue.js',
            'jbassets:js/admin/menu.js',
            'jbassets:js/back-end.js',
        ));
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
        static $isAdded;

        if (!isset($isAdded)) {
            $isAdded = true;

            $this->jQuery();
            $this->addScript(implode("\n\t", array(
                'JBZoo.DEBUG = 1;',
                'jQuery.migrateMute = false;',
            )), false);

            $this->js(array(
                'jbassets:js/helper.js',
                'jbassets:js/widget.js',
                'jbassets:js/jbzoo.js',
                'jbassets:js/front-end.js',
                'jbassets:js/widget/goto.js',
                'jbassets:js/widget/select.js',
                'jbassets:js/libs/cookie.js',
            ));
        }
    }

    /**
     * Include UIkit files
     */
    public function uikit($addJS = false)
    {
        if ($addJS) {
            $this->js('jbassets:js/libs/uikit.min.js');
        }

        $this->css('jbassets:css/uikit.min.css');
    }

    /**
     * @param string $id
     * @param array  $params
     */
    public function currencyToggle($id, $params = array())
    {
        $this->less('jbassets:less/widget/currencytoggle.less');
        $this->js(array(
            'jbassets:js/widget/money.js',
            'jbassets:js/widget/currencytoggle.js'
        ));

        $this->addVar('currencyList', $this->app->jbmoney->getData());
        $this->addScript('$("#' . $id . '").JBZooCurrencyToggle(' . $this->toJSON($params) . ')');
    }

    /**
     * Init filter assets
     */
    public function filter()
    {
        $this->tools();
        $this->css('jbassets:css/jbzoo.filter.css');
    }

    /**
     * Init filter assets
     */
    public function filterProps()
    {
        $this->tools();
        $this->css('jbassets:css/jbzoo.filter.css');
    }

    /**
     * Include jQuery UI lib
     */
    public function jQueryUI()
    {
        $this->jQuery();

        static $isAdded;

        if (!isset($isAdded)) {
            $isAdded = true;
            $this->css('libraries:jquery/jquery-ui.custom.css');
            $this->js('libraries:jquery/jquery-ui.custom.min.js');
        }
    }

    /**
     * Include fancybox lib
     */
    public function fancybox()
    {
        $this->jQuery();
        $this->css('jbassets:css/libraries.css');
        $this->js(array(
            'jbassets:js/libs/fancybox/core.js',
            'jbassets:js/libs/fancybox/buttons.js',
            'jbassets:js/libs/fancybox/media.js',
            'jbassets:js/libs/fancybox/thumbnail.js',
        ));
    }

    /**
     * Include table sorter lib
     */
    public function tablesorter()
    {
        $this->jQuery();
        $this->css('jbassets:css/libraries.css');
        $this->js('jbassets:js/libs/tablesorter.js');
    }

    /**
     * Include chosen lib
     */
    public function chosen()
    {
        $this->jQuery();
        $this->css('jbassets:css/libraries.css');
        $this->js('jbassets:js/libs/chosen.js');
    }

    /**
     * Include datepicker lib
     */
    public function datepicker()
    {
        $this->jQueryUI();
        $this->css('libraries:jquery/plugins/timepicker/timepicker.css');
        $this->js('libraries:jquery/plugins/timepicker/timepicker.js');
    }

    /**
     * Include datepicker lib
     */
    public function nivoslider()
    {
        $this->jQuery();
        $this->css('jbassets:css/libraries.css');
        $this->js('jbassets:js/libs/nivoslider.js');
    }

    /**
     * Include jQuery framework
     */
    public function jQuery()
    {
        static $isAdded;

        if (!isset($isAdded)) {
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
            'jbassets:less/cart/cart.less'
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
            'elements:jbcompare/assets/js/compare-table.js'
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
     */
    public function calendar()
    {
        $this->jQueryUI();
        $this->js('libraries:jquery/plugins/timepicker/timepicker.js');
    }

    /**
     * Include jQuery favorite
     */
    public function favorite()
    {
        $this->tools();
        $this->js(array(
            'elements:jbfavorite/assets/js/favorite-buttons.js',
            'elements:jbfavorite/assets/js/favorite-list.js'
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
        $this->js('jbassets:js/widget/quantity.js');
        $this->less('jbassets:less/widget/quantity.less');

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
     * @param $id
     * @param $options
     */
    public function initQuantity($id, $options)
    {
        $this->tools();
        $this->quantity();
        $this->addScript('$("#' . $id . '").JBZooQuantity(' . $this->toJSON($options) . ')');
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
        static $isAdded;

        $this->jQuery();
        $this->fancybox();

        if (!isset($isAdded)) {
            $isAdded = true;

            $params = array(
                'helpers' => array(
                    'title'   => array('type' => 'outside'),
                    'buttons' => array('position' => 'top'),
                    'thumbs'  => array('width' => 80, 'height' => 80),
                    'overlay' => array('locked' => false),
                )
            );

            $this->addScript('$("a.jbimage-link[rel=jbimage-popup], a.jbimage-gallery").fancybox(' . $this->toJSON($params) . ')');
        }
    }

    /**
     * Height fix for items columns
     * @param string $element
     */
    public function heightFix($element = '.column')
    {
        static $isAdded;

        $this->jQuery();
        $this->js('jbassets:js/widget/heightfix.js');

        if (!isset($isAdded)) {
            $isAdded = true;
            $this->addScript('$(".jbzoo .items, .jbzoo .subcategories, .jbzoo .related-items").JBZooHeightFix({element : "' . $element . '"})');
        }
    }

    /**
     * Add to script
     */
    public function addRootUrl()
    {
        static $isAdded;
        if (!isset($isAdded)) {
            $isAdded = true;
            $this->addVar('rootUrl', JURI::root());
        }
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
        static $vars;

        $vars = isset($vars) ? $vars : array();

        if (!isset($vars[$varName])) {
            $vars[$varName] = true;
            $this->addScript("JBZoo.addVar('" . $varName . "', " . $this->toJSON($value) . ")", false);
        }
    }

    /**
     * Init select cascade
     */
    public function initSelectCascade()
    {
        $this->js('jbassets:js/widget/select-cascade.js');
    }

    /**
     * Init script for JBCascadeSelect
     * @param string $uniqid
     * @param string $itemList
     */
    public function initJBCascadeSelect($uniqid, $itemList)
    {
        static $isAdded;
        $this->jQuery();
        $this->initSelectCascade();

        if (!isset($isAdded)) {
            $isAdded = array();
        }

        if (!isset($isAdded[$uniqid])) {

            $params = $this->toJSON(array(
                'items'    => $itemList,
                'uniqid'   => $uniqid,
                'text_all' => ' - ' . JText::_('JBZOO_ALL') . ' - ',
            ));

            $this->addScript('$(".jbcascadeselect-wrapper.jbcascadeselect-' . $uniqid . '").JBCascadeSelect(' . $params . ')');

            $isAdded[$uniqid] = true;
        }
    }

    /**
     * Init jqueryui autocomplete
     */
    public function initAutocomplete()
    {
        static $isAdded;

        $this->jQuery();
        $this->jQueryUI();

        if (!isset($isAdded)) {
            $isAdded = true;
            $this->addScript('$(".jbzoo .jsAutocomplete").each(function (n, obj) {
                var $input = $(obj),
                    $form = $input.closest("form");

                $input.autocomplete({
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
        static $isAdded;

        $this->jQuery();
        $this->jQueryUI();

        if (!isset($isAdded)) {
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
                        var element = $(".jsPriceAutoCompleteValue", $(this).parent());

                        if(ui.item) {
                            element.val(ui.item.id).removeAttr("disabled");
                        } else {
                            element.val(null).attr("disabled", true);
                        }
                    },
                    select: function(event, ui) {
                        var element = $(".jsPriceAutoCompleteValue", $(this).parent());

                        if(ui.item) {
                            element.val(ui.item.id).removeAttr("disabled");
                        } else {
                            element.val(null).attr("disabled", true);
                        }
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
        static $isAdded;

        $this->accordion();

        if (!isset($isAdded)) {
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
     */
    public function initJBColorHelper($queryElement, $type = true)
    {
        $this->colors();

        // force include for back-end. Do not delete!
        $this->less('jbassets:less/general.less');

        if ($queryElement) {
            $this->addScript('$("#' . $queryElement . '").JBZooColors({multiple: "' . (boolean)$type . '"})');
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
            $this->js('media:jui/js/jquery.minicolors.min.js');
            $this->css('media:jui/css/jquery.minicolors.css');
            $this->addScript('$("' . $queryElement . '").JBColorElement({message: "' . $text . '"})');
        }
    }

    /**
     * Init tooltip
     */
    public function initTooltip()
    {
        static $isAdded;

        $this->jQueryUI();

        if (!isset($isAdded)) {
            $isAdded = true;
            $this->addScript('$(".jbzoo .jbtooltip").tooltip()');
        }
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

        $this->addScript('$("' . $queryElement . '").JBZooDelimiter({
            "version": "' . $version . '"
        })');
    }

    /**
     * Add script to document
     * @param      $script
     * @param bool $docReady
     */
    public function addScript($script, $docReady = true)
    {
        if (!$this->app->jbrequest->isAjax()) {

            if ($docReady) {
                $script = "\tjQuery(function($){ " . $script . "; });\n";
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
     * Include JS in document
     * @param array  $files
     * @param string $group
     * @return bool
     */
    public function js($files, $group = 'default')
    {
        return $this->_include((array)$files, 'js', $group);
    }

    /**
     * Include CSS in document
     * @param array  $files
     * @param string $group
     * @return bool
     */
    public function css($files, $group = 'default')
    {
        return $this->_include((array)$files, 'css', $group);
    }

    /**
     * @param        $files
     * @param string $group
     * @return bool
     */
    public function less($files, $group = 'default')
    {
        $files = (array)$files;

        $resultFiles = array();
        foreach ($files as $file) {
            $resultFiles[] = $this->app->jbless->compile($file);
        }

        return $this->_include((array)$resultFiles, 'css', $group);
    }

    /**
     * @param $vars
     * @return mixed|string
     */
    public function toJSON($vars)
    {
        return json_encode((object)$vars);
    }

    /**
     * Include files to document
     * @param array $files
     * @param       $type
     * @param       $group
     * @return bool
     */
    protected function _include(array $files, $type, $group)
    {
        if (
            empty($files)
            || $this->app->jbrequest->isAjax()
            || $this->app->jbrequest->is('format', 'feed')
        ) {
            return false;
        }

        foreach ($files as $file) {

            $isExternal = strpos($file, 'http') !== false;

            $filePath = $file;
            if (!$isExternal) {
                $fullPath = $this->app->path->path($file);
                $filePath = $this->app->path->url($file);
            }

            if ($filePath) {
                if (!$isExternal) {
                    $filePath = $filePath . '?' . substr(filemtime($fullPath), -2);
                    $filePath = $this->_getRoot() . $this->app->path->relative($filePath);
                }

                if ($type == 'css') {
                    JFactory::getDocument()->addStylesheet($filePath);
                } elseif ($type == 'js') {
                    JFactory::getDocument()->addScript($filePath);
                }
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
        static $isAdded;

        if (!isset($isAdded)) {
            $isAdded = true;
            $this->addScript('$(".jbzoo a").attr("target", "_top")');
        }
    }
}
