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
 * Class JBMinifierHelper
 */
class JBMinifierHelper extends AppHelper
{
    /**
     * @type string
     */
    protected $_cachePath = '';

    /**
     * @type JBFileHelper
     */
    protected $_jbfile = null;

    /**
     * @param App $app
     */
    public function __construct($app)
    {
        parent::__construct($app);

        $this->_cachePath = JPath::clean($this->app->path->path('root:') . '/cache/jbzoo_css');
        $this->_jbfile    = $this->app->jbfile;
    }

    /**
     * @param array  $files
     * @param string $type
     * @return string
     */
    public function split($files = array(), $type = 'css')
    {
        // build hash
        $hashArr = array('files' => $files, 'hashes' => array());
        foreach ($files as $orig => $file) {
            $hashArr['hashes'][] = md5_file(JPATH_ROOT . '/' . $file);
        }
        $hash = md5(serialize($hashArr));

        $cachePath = $this->_cachePath . '/' . $hash . '.' . $type;
        $relCache  = $this->app->path->relative($cachePath);
        if (!JFile::exists($cachePath)) {

            $content = '';
            foreach ($files as $orig => $file) {
                $content .= $this->_jbfile->read(JPATH_ROOT . '/' . $file) . ';' . PHP_EOL;
            }

            $content =
                '/* **********' . PHP_EOL
                . implode(PHP_EOL, array_keys($files)) . PHP_EOL
                . '********** */' . PHP_EOL . PHP_EOL
                . $content;

            $this->_jbfile->save($cachePath, $content);
        }

        return $relCache;
    }

}
