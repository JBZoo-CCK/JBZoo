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
     * @var Less_Parser
     */
    protected $_precessor;

    /**
     * @var string
     */
    protected $_lessFull = '';
    protected $_lessRel = '';
    protected $_minFull = '';

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
        $this->_lessFull = JPath::clean($this->app->path->path('jbassets:'));
        $this->_lessRel  = JUri::root() . $this->app->path->relative($this->_lessFull);

        $this->_minFull = JPath::clean($this->app->path->path('jbassets:min/css'));
        $this->_minRel  = JUri::root() . $this->app->path->relative($this->_minFull);
    }

    /**
     * @param $virtPath
     * @return null|string
     */
    public function compile($virtPath)
    {
        $origFull = $this->app->path->path($virtPath);
        if (!$origFull) {
            return null;
        }

        $origFull = JPath::clean($origFull);
        $origRel  = $this->app->path->relative($origFull);
        $debug    = $this->_isDebug();

        $hash     = $this->_getHash($origFull);
        $filename = sha1($virtPath) . ($debug ? '-debug' : '') . '.css';

        $relPath   = $this->_minRel . '/' . $filename;
        $cachePath = JPath::clean($this->_minFull . '/' . $filename);

        $updateFile = false;
        if (JFile::exists($cachePath)) {

            // quickest way for getting first file line
            $cacheRes  = fopen($cachePath, 'r');
            $firstLine = fgets($cacheRes);
            fclose($cacheRes);

            // check cacheid
            if (!preg_match('#' . $hash . '#i', $firstLine)) {
                $updateFile = true;
            }

        } else {
            $updateFile = true;
        }

        if ($updateFile) {
            $css =
                '/* cacheid:' . $hash . " */\n" .
                '/* path:' . $origRel . " */\n" .
                $this->_compile($origFull);

            $this->_save($cachePath, $css);
        }

        if (filesize($cachePath) > 5) {
            return $relPath;
        }

        return null;
    }

    /**
     * @param $path
     * @return mixed
     */
    protected function _compile($path)
    {
        try {

            if (!$this->_precessor) {
                $this->_precessor = $this->_getProcessor();
            }

            $miscPath = JPath::clean($this->app->path->path('jbassets:less'));
            $imports  = '';
            foreach ($this->_import as $import) {
                $imports .= '@import "' . $miscPath . '/' . $import . '";' . "\n";
            }

            $rel  = JURi::root() . $this->app->path->relative($path);
            $code = $imports . "\n" . file_get_contents($path);
            $this->_precessor->parse($code, $rel);
            $resultCss = $this->_precessor->getCss();

            return $resultCss;

        } catch (Exception $ex) {
            die ('<strong>Less Error (JBZoo):</strong><br/><pre>' . $ex->getMessage() . '</pre>');
        }
    }

    /**
     * @param $file
     * @param $data
     * @return bool
     */
    protected function _save($file, $data)
    {
        $dir = dirname($file);
        if (!JFolder::exists($dir)) {
            JFolder::create($dir);
        }

        return JFile::write($file, $data);
    }

    /**
     * @param $mainPath
     * @return string
     */
    protected function _getHash($mainPath)
    {
        static $importHash;

        if (!isset($importHash)) {
            $path   = $this->app->path->path('jbassets:less');
            $result = array();
            foreach ($this->_import as $import) {
                $filePath = JPath::clean($path . '/' . $import);
                $result[] = sha1_file($filePath);
            }

            $importHash = implode(':', $result);
        }

        return $importHash . ':' . sha1_file($mainPath);
    }

    /**
     * @return Less_Parser
     */
    protected function _getProcessor()
    {

        if (!class_exists('Less_Parser')) {
            require_once JPATH_ROOT . '/media/zoo/applications/jbuniversal/framework/libs/less.gpeasy.php';
        }

        $options = array(
            'compress'     => 1, // option - whether to compress
            'strictUnits'  => 0, // whether units need to evaluate correctly
            'strictMath'   => 0, // whether math has to be within parenthesis
            'relativeUrls' => 1, // option - whether to adjust URL's to be relative
            'numPrecision' => 4,
            'cache_method' => 0,
            'sourceMap'    => 0,
        );

        if ($this->_isDebug()) {
            $options['compress'] = 0;
            // TODO add source map
        }

        $this->_precessor = new Less_Parser($options);

        $this->_precessor->SetImportDirs(array(
            $this->_lessFull => $this->_lessRel,
            JPATH_ROOT       => JUri::root(),
        ));

        return $this->_precessor;
    }

    /**
     * Check debug state
     * @return bool
     */
    protected function _isDebug()
    {
        return defined('JDEBUG') && JDEBUG;
    }

}
