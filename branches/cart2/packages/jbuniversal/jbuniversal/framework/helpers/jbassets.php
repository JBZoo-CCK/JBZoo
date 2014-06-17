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
 * Class JBAssetsHelper
 */
class JBAssetsHelper extends AppHelper
{
    /**
     * @var JDocumentHTML
     */
    protected $_document = null;

    /**
     * @var int
     */
    protected $_isCaching = null;

    /**
     * Constructor
     * @param $app
     */
    public function __construct($app)
    {
        parent::__construct($app);
        $this->_document  = JFactory::getDocument();
        $this->_isCaching = $this->app->jbcache->isEnabled();
    }

    /**
     * Set application styles files
     * @param string $alias
     */
    public function setAppCss($alias = null)
    {
        $this->_include(array(
            'jbassets:css/jbzoo.css',
            'jbassets:css/jbzoo.' . $alias . '.css'
        ), 'css');
    }

    /**
     * Add script and styles for back-end
     */
    public function admin()
    {
        if (!$this->app->jbenv->isSite()) {
            $this->jQuery();
            $this->_include(array('jbassets:css/admin.css',), 'css');
            $this->_include(array('jbassets:js/admin.js'), 'js');
        }
    }

    /**
     * Set application JavaScript files
     * @param string $alias
     */
    public function setAppJS($alias = null)
    {
        $this->tools();
        $this->_include(array(
            'jbassets:js/jbzoo.' . $alias . '.js'
        ), 'js');
    }

    /**
     * Include JBZoo Tools script
     */
    public function tools()
    {
        $this->jQuery();

        if (defined('JDEBUG') && JDEBUG) {
            $this->_include(array('jbassets:js/jquery.jbzootools.orig.js'), 'js');
        } else {
            $this->_include(array('jbassets:js/jquery.jbzootools.min.js'), 'js');
        }
    }

    /**
     * Include UIkit files
     */
    public function uikit($addJS = false)
    {
        if (defined('JDEBUG') && JDEBUG) {

            if ($addJS) {
                $this->_include(array('jbassets:js/uikit.orig.js'), 'js');
            }

            $this->_include(array('jbassets:css/uikit.orig.css'), 'css');

        } else {

            if ($addJS) {
                $this->_include(array('jbassets:js/uikit.min.js'), 'js');
            }

            $this->_include(array('jbassets:css/uikit.min.css'), 'css');
        }
    }

    /**
     * Init filter assets
     * @param $alias
     */
    public function filter($alias = 'default')
    {
        $this->tools();
        $this->_include(array(
            'jbassets:js/jbzoo.filter.js',
            'jbassets:js/jbzoo.filter.' . $alias . '.js'
        ), 'js');

        $this->_include(array(
            'jbassets:css/jbzoo.css',
            'jbassets:css/jbzoo.filter.css',
            'jbassets:css/jbzoo.filter.' . $alias . '.css'
        ), 'css');
    }

    /**
     * Init filter assets
     * @param $alias
     */
    public function filterProps($alias = 'default')
    {
        $this->tools();
        $this->_include(array(
            'jbassets:js/jbzoo.filter.js',
            'jbassets:js/jbzoo.filter.' . $alias . '.js'
        ), 'js');

        $this->_include(array(
            'jbassets:css/jbzoo.css',
            'jbassets:css/jbzoo.filter.css',
            'jbassets:css/jbzoo.filter.' . $alias . '.css'
        ), 'css');
    }

    /**
     * Include
     * @param $type
     */
    public function itemStyle($type)
    {
        static $isAdded;

        if (!isset($isAdded[$type]) && $type) {
            $this->_include(array('jbassets:js/jbzoo.' . $type . '.js'), 'js');
            $this->_include(array('jbassets:css/jbzoo.' . $type . '.css'), 'css');

            if (!isset($isAdded)) {
                $isAdded = array();
            }

            $isAdded[$type] = true;
        }
    }

    /**
     * Include jQuery UI lib
     */
    public function jQueryUI()
    {
        $this->jQuery();
        $this->_include(array('libraries:jquery/jquery-ui.custom.css',), 'css');
        $this->_include(array('libraries:jquery/jquery-ui.custom.min.js'), 'js');
    }

    /**
     * Include fancybox lib
     */
    public function fancybox()
    {
        $this->jQuery();
        $this->_include(array('jbassets:css/libraries.css'), 'css');
        $this->_include(array('jbassets:js/jquery.libraries.min.js',), 'js');
    }

    /**
     * Include table sorter lib
     */
    public function tablesorter()
    {
        $this->jQuery();
        $this->_include(array('jbassets:css/libraries.css'), 'css');
        $this->_include(array('jbassets:js/jquery.libraries.min.js',), 'js');
    }

    /**
     * Include chosen lib
     */
    public function chosen()
    {
        $this->jQuery();
        $this->_include(array('jbassets:css/libraries.css'), 'css');
        $this->_include(array('jbassets:js/jquery.libraries.min.js',), 'js');
    }

    /**
     * Include datepicker lib
     */
    public function datepicker()
    {
        $this->jQueryUI();
        $this->_include(array('libraries:jquery/plugins/timepicker/timepicker.css',), 'css');
        $this->_include(array('libraries:jquery/plugins/timepicker/timepicker.js'), 'js');
    }

    /**
     * Include datepicker lib
     */
    public function nivoslider()
    {
        $this->jQuery();
        $this->_include(array('jbassets:css/libraries.css'), 'css');
        $this->_include(array('jbassets:js/jquery.libraries.min.js',), 'js');
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
    }

    /**
     * Include jQuery compare
     */
    public function jQueryCompare()
    {
        $this->tools();
    }

    /**
     * Include progress bar in document
     */
    public function progressBar()
    {
        $this->jQueryUI();
        $this->tools();
    }

    /**
     * Include tabs widget in document
     */
    public function tabs()
    {
        $this->tools();
        $this->_include(array('jbassets:css/jbzoo.css'), 'css');
    }

    /**
     * Include tabs widget in document
     */
    public function accordion()
    {
        $this->tools();
        $this->_include(array('jbassets:css/jbzoo.css'), 'css');
    }

    /**
     * Init jbzoo compare
     */
    public function initJBCompare()
    {
        static $isAdded;

        $this->jQuery();
        $this->jQueryCompare();

        if (!isset($isAdded)) {
            $isAdded = true;
            $this->addScript('jQuery(function($){ $(".jbzoo .jsJBZooCompare").JBCompareButtons(); });');
        }
    }

    /**
     * Include jQuery favorite
     */
    public function jQueryFavorite()
    {
        $this->tools();
    }

    /**
     * Init JBprice Advance plugin
     */
    public function initJBpriceAdvance()
    {
        $this->tools();
    }

    /**
     * Init JBZoo favorite
     */
    public function initJBFavorite()
    {
        static $isAdded;

        $this->jQueryFavorite();

        if (!isset($isAdded)) {
            $isAdded = true;
            $this->addScript('jQuery(function($){ $(".jbzoo .jsJBZooFavorite").JBFavoriteButtons(); });');
        }
    }

    /**
     * Assets for payment page
     */
    public function payment()
    {

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
            $this->addScript('jQuery(function($){
                $("a.jbimage-link[rel=jbimage-popup], a.jbimage-gallery").fancybox({
                    "helpers" : {
                        "title"  : { type : "outside" },
                        "buttons": { position:"top" },
                        "thumbs" : { width :80, height:80 },
                        "overlay": { locked: false}
                    }
                });
            });');
        }
    }

    /**
     * Height fix for items columns
     */
    public function heightFix()
    {
        static $isAdded;

        $this->jQuery();

        if (!isset($isAdded)) {

            $isAdded = true;
            $this->addScript('jQuery(function($){
                $(".jbzoo .items").JBZooHeightFix();
                $(".jbzoo .subcategories").JBZooHeightFix();
                $(".jbzoo .related-items").JBZooHeightFix();
            });');
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
            $this->addVar('JB_URL_ROOT', JURI::root());
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
        $this->addScript('var ' . $varName . ' = ' . json_encode($value) . "; \n \n ");
    }

    /**
     * Init select cascade
     */
    public function initSelectCascade()
    {
        $this->tools();
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

        if (!isset($isAdded)) {
            $isAdded = array();
        }

        if (!isset($isAdded[$uniqid])) {

            $this->addScript('jQuery(function($){
                $(".jbcascadeselect-wrapper.jbcascadeselect-' . $uniqid . '").JBCascadeSelect({
                    "items": ' . json_encode($itemList) . ',
                    "uniqid" : "' . $uniqid . '",
                    "text_all" : " - ' . JText::_('JBZOO_ALL') . ' - "
                });
            });');

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
            $this->addScript('jQuery(function($){
                $(".jbzoo .jsAutocomplete").each(function (n, obj) {
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
            $this->addScript('jQuery(function($){
                $(".jbzoo .jsAccordion").each(function(n, obj){
                    var $obj = $(obj),
                        id   = "jbaccordion-" + n;
                    $obj.attr("id", id);
                    $("#" + id).JBZooAccordion();
                });
            })');
        }
    }

    /**
     * Init price widget
     */
    public function initJBPrice()
    {
        static $isAdded;

        $this->tools();

        if (!isset($isAdded)) {
            $isAdded = true;
            $this->addScript('jQuery(function($){ $(".jbzoo .jsPrice").JBZooPrice(); });');
        }
    }

    /**
     * Init color widget
     * @param string $queryElement
     * @param boolean  $type
     */
    public function initJBColorHelper($queryElement, $type = true)
    {
        $this->jQuery();

        // included in the back-end. Do not delete.
        $this->_include(array('jbassets:css/jbzoo.css'), 'css');
        $this->tools();

        if($queryElement) {
            $this->addScript('jQuery(function($){
                $("#'. $queryElement .'").JBColorHelper({multiple: "' . (boolean) $type . '"});
            });');
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

        if($queryElement) {
            $this->addScript('jQuery(document).ready(function($){
                $("'. $queryElement .'").JBColorElement({message: "' .$text. '"});
            });');
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
            $this->addScript('jQuery(document).ready(function($){
                $(".jbzoo .jbtooltip").tooltip();
            });');
        }
    }

    public function initJBDelimiter($queryElement, $version = null)
    {
        $this->jQuery();

        if(empty($version)) {
            $version = JString::substr($this->app->jbversion->joomla(), 0, 1);
        }

        $this->addScript('jQuery(document).ready(function($){
                $("' . $queryElement . '").JBZooDelimiter({
                    "version": "' . $version . '"
                });
            });'
        );
    }

    /**
     * Add script to document
     * @param string $script
     */
    public function addScript($script)
    {
        if (!$this->app->jbrequest->isAjax()) {
            $this->_document->addScriptDeclaration("\n" . $script);
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
            $jUri = JURI::getInstance();
            $root = $jUri->getScheme() . '://' . $jUri->getHost() . '/';
            $root = '/';
        }

        return $root;
    }

    /**
     * Include JS in document
     * @param $files
     * @return bool
     */
    public function js($files)
    {
        return $this->_include((array)$files, 'js');
    }

    /**
     * Include CSS in document
     * @param $files
     * @return bool
     */
    public function css($files)
    {
        return $this->_include((array)$files, 'css');
    }

    /**
     * Include files to document
     * @param array $files
     * @param $type
     * @return bool
     */
    protected function _include(array $files, $type)
    {
        if ($this->app->jbrequest->is('format', 'feed')) {
            return;
        }

        static $includedFiles;

        if (!isset($includedFiles)) {
            $includedFiles = array();
        }

        if (count($files) && !$this->app->jbrequest->isAjax()) {
            foreach ($files as $file) {

                $isExternal = strpos($file, 'http') !== false;

                $filePath = $file;
                if (!$isExternal) {
                    $fullPath = $this->app->path->path($file);
                    $filePath = $this->app->path->url($file);
                }

                if ($filePath) {

                    if (!$isExternal) {
                        $filePath = $filePath . '?ver=' . date("Ymd", filemtime($fullPath));
                        $filePath = $this->_getRoot() . $this->app->path->relative($filePath);
                    }

                    if ($type == 'css') {
                        $this->_document->addStylesheet($filePath);

                    } elseif ($type == 'js') {
                        $this->_document->addScript($filePath);
                    }

                }
            }

            return true;
        }

        return false;
    }

    /**
     * Init modal window
     * @param string $class
     * @param array $opt
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
            $this->addScript('jQuery(function($){
                    $(".jbzoo a").attr("target", "_top");
                });
            ');
        }
    }
}
