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
    protected $_forceUpdate = false;

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
        $this->_lessFull = JPath::clean($this->app->path->path('jbapp:assets/less'));
        $this->_lessRel  = JUri::root() . $this->app->path->relative($this->_lessFull);

        $this->_minFull = JPath::clean($this->app->path->path('root:') . '/cache/jbzoo_css');
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

        if ($updateFile || $this->_forceUpdate) {
            $css = '/* cacheid:' . $hash . " */\n" .
                '/* path:' . $virtPath . " */\n" .
                $this->_compile($origFull);

            $this->_save($cachePath, $css);
        }

        if (filesize($cachePath) > 5) {
            $mtime = substr(filemtime($cachePath), -2);
            return $relPath . '?' . $mtime;
        }

        return null;
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

            if (!$this->_isDebug()) {
                $resultCss = $this->_forceCompress($resultCss);
            }

            return $resultCss;

        } catch (Exception $e) {
            die ('<strong>Less Error (JBZoo):</strong><br/><pre>' . $e->getMessage() . '</pre>');
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
            $result = array();
            foreach ($this->_import as $import) {
                $path     = JPath::clean($this->app->path->path('jbassets:less/' . $import));
                $result[] = sha1_file($path);
            }

            $importHash = implode(':', $result);
        }

        return $importHash . ':' . sha1_file($mainPath);
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

        $precessor = new Less_Parser($options);

        $paths = array_merge(array(
            $this->_lessFull => $this->_lessRel,
            JPATH_ROOT       => JUri::root(),
        ), $addPath);

        $precessor->SetImportDirs($paths);

        return $precessor;
    }

    /**
     * @param $code
     * @return mixed|string
     */
    public function _forceCompress($code)
    {
        $code = (string)$code;

        // remove comments
        // $code = preg_replace('#/\*[^*]*\*+([^/][^*]*\*+)*/#ius', '', $code); // exp

        // remove tabs, spaces, newlines, etc.
        $code = str_replace(array("\r\n", "\r", "\n", "\t", '  ', '    ', '    '), '', $code);

        $code = str_replace(' {', '{', $code); // spaces
        $code = str_replace('{ ', '{', $code); // spaces
        $code = str_replace(' }', '}', $code); // spaces
        $code = str_replace('; ', ';', $code); // spaces
        $code = str_replace(';;', ';', $code); // typos
        $code = str_replace(';}', '}', $code); // last ";"

        // remove space after colons
        $code = preg_replace('#([a-z\-])(:\s*|\s*:\s*|\s*:)#ius', '$1:', $code);

        // spaces before "!important"
        $code = preg_replace('#(\s*\!important)#ius', '!important', $code);

        // trim
        $code = JString::trim($code);

        return $code;
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
