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
 * Class JBCheckFilesHelper
 */
class JBCheckFilesHelper extends AppHelper
{
    /**
     * Check paths
     * @var array
     */
    protected $_checkVirtPaths = array(
        'jbapp',
        'mod_jbzoo_basket',
        'mod_jbzoo_props',
        'mod_jbzoo_search',
        'mod_jbzoo_category',
        'plugin_jbzoo'
    );

    /**
     * Exclude pattern list
     * @var array
     */
    protected $_exclude = array(
        '\.config$',
        'config[/\\\]licence\.php',
        'config[/\\\]config\.php',
        'config[/\\\]licence\..*\.php',
        'renderer[/\\\]item[/\\\]',
        'app_icons',
        'css[/\\\]jbzoo\..*\.css',
        'js[/\\\]jbzoo\..*\.js',
        '\.jbsample',
        '\.jbsamplerepeatable',
        'yml_config\.php'
    );

    /**
     * Create checksums file
     */
    public function create()
    {
        $checksums = $this->app->path->path('jbapp:') . 'checksums';

        if (JFile::exists($checksums)) {
            JFile::delete($checksums);
        }

        foreach ($this->_checkVirtPaths as $vpath) {
            if ($path = $this->app->path->path($vpath . ':')) {
                $this->app->jbchecksum->create($path, $checksums, $vpath, $this->_exclude);
            }
        }

    }

    /**
     * Checks for ZOO modifications.
     * @return array modified files
     * @throws JBCheckFilterException
     */
    public function check()
    {
        if (!$checksum = $this->app->path->path('jbapp:checksums')) {
            $path = $this->app->path->path('jbapp:');
            throw new JBCheckFilterException(JText::_('Unable to locate checksums file in ' . $path));
        }

        $result = array();

        foreach ($this->_checkVirtPaths as $vpath) {

            $path = $this->app->path->path($vpath . ':');

            if ($path) {
                $this->app->jbchecksum->verify(
                    $path,
                    $checksum,
                    $result,
                    array(create_function(
                        '$path',
                        'if (preg_match("#^' . $vpath . '#", $path)) return preg_replace("#^' . $vpath . '/#", "", $path);'
                    )),
                    $this->app->path->relative($path),
                    $this->_exclude
                );
            }
        }

        return $result;
    }

}

class JBCheckFilterException extends AppException
{
}