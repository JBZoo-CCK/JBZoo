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
 * Class JBLessHelper
 */
class JBLessHelper extends AppHelper
{
    /**
     * @type bool
     */
    protected $_force = false;

    /**
     * @var string
     */
    protected $_lessFull = '';
    protected $_lessRel = '';
    protected $_minFull = '';

    /**
     * @type JBCacheHelper
     */
    protected $_jbcache = null;

    /**
     * Force import for each less file
     * @var array
     */
    protected $_import = array(
        'misc/variables.less',
        'misc/mixins.less',
        'misc/aliases.less',
    );

    /**
     * @param App $app
     */
    public function __construct($app)
    {
        parent::__construct($app);

        $this->_lessFull = JPath::clean($this->app->path->path('jbapp:assets/less'));
        $this->_lessRel  = JUri::root() . $this->app->path->relative($this->_lessFull);

        $this->_minFull = JPath::clean($this->app->path->path('root:') . '/cache/jbzoo_assets');
        $this->_minRel  = $this->app->path->relative($this->_minFull);

        $this->_force   = (int)JBModelConfig::model()->get('less_mode', '0', 'config.assets');
        $this->_jbcache = $this->app->jbcache;
    }

    /**
     * @param $virtPath
     * @return null|string
     */
    public function compile($virtPath)
    {
        static $compiledList = array();

        if (!isset($compiledList[$virtPath])) {

            $origFull = $this->app->path->path($virtPath);
            if (!$origFull) {
                return null;
            }

            $hash      = $this->_getHash($origFull);
            $filename  = $this->_jbcache->getFileName($origFull) . (JDEBUG ? '-debug' : '') . '.css';
            $cachePath = JPath::clean($this->_minFull . '/' . $filename);

            if (!$this->_jbcache->checkAsset($cachePath, $hash) || $this->_force) {
                $cssContent = $this->_compile($origFull);
                $this->_jbcache->saveAsset($cachePath, $cssContent, $hash);
            }

            $compiledList[$virtPath] = $this->_minRel . '/' . $filename;
        }

        return $compiledList[$virtPath];
    }

    /**
     * @param $lessPath
     * @return mixed
     */
    protected function _compile($lessPath)
    {
        try {
            $relative  = rtrim(JUri::root(), '/') . '/' . ltrim($this->app->path->relative($lessPath), '/');
            $precessor = $this->_getProcessor();
            $precessor->parseFile($lessPath, $relative);
            $resultCss = $precessor->getCss();

            return $resultCss;

        } catch (Exception $e) {
            die ('<strong>Less Error (JBZoo):</strong><br/><pre>' . $e->getMessage() . '</pre>');
        }
    }

    /**
     * @param $mainPath
     * @return string
     */
    protected function _getHash($mainPath)
    {
        static $importHash;

        if (!isset($importHash)) {
            $result = array(md5(JURI::root())); // for paths in CSS
            foreach ($this->_import as $import) {
                $path     = JPath::clean($this->app->path->path('jbassets:less/' . $import));
                $result[] = md5_file($path);
            }

            $importHash = implode(':', $result);
        }

        return $importHash . ':' . md5_file($mainPath);
    }

    /**
     * @param array $addPath
     * @return Less_Parser
     */
    protected function _getProcessor($addPath = array())
    {
        if (!class_exists('Less_Parser')) {
            require_once JPATH_ROOT . '/media/zoo/applications/jbuniversal/framework/libs/less.gpeasy.php';
        }

        $options = array(
            'compress'     => 0, // option - whether to compress
            'strictUnits'  => 0, // whether units need to evaluate correctly
            'strictMath'   => 0, // whether math has to be within parenthesis
            'relativeUrls' => 1, // option - whether to adjust URL's to be relative
            'numPrecision' => 4,
            'cache_method' => 0,
            'sourceMap'    => 0,
        );

        $precessor = new Less_Parser($options);

        $paths = array_merge(array(
            $this->_lessFull => $this->_lessRel,
            JPATH_ROOT       => JUri::root(),
        ), $addPath);

        $precessor->SetImportDirs($paths);

        return $precessor;
    }

}
