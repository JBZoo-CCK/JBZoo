<?php
/**
 * JBZoo Application
 *
 * This file is part of the JBZoo CCK package.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package    Application
 * @license    GPL-2.0
 * @copyright  Copyright (C) JBZoo.com, All rights reserved.
 * @link       https://github.com/JBZoo/JBZoo
 * @author     Denis Smetannikov <denis@jbzoo.com>
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
    protected $_checkVirtPaths = [
        'jbapp',
        'mod_jbzoo_basket',
        'mod_jbzoo_category',
        'mod_jbzoo_currency',
        'mod_jbzoo_item',
        'mod_jbzoo_props',
        'mod_jbzoo_search',
        'plugin_jbzoo'
    ];

    /**
     * Exclude pattern list
     * @var array
     */
    protected $_exclude = [
        // old files 
        'index\.html$',

        // any configs
        '\.config$',

        // jbzoo version
        'application\.xml$',

        // templates
        'templates[/\\\].*[/\\\]assets',
        'templates[/\\\].*[/\\\]cart-elements',
        'templates[/\\\].*[/\\\]elements',
        'templates[/\\\].*[/\\\]helpers',
        'templates[/\\\].*[/\\\]language',
        'templates[/\\\].*[/\\\]templates-system',
        'renderer[/\\\].*[/\\\][a-z0-9\-]*.php',
        'app_icons',

        // item templates
        'positions\.xml$',
        'metadata\.xml',
        'renderer[/\\\]item[/\\\].*',

        // temp files
        'tmp[/\\\].*',
    ];

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

        $result = [];

        foreach ($this->_checkVirtPaths as $vPath) {
            $path = $this->app->path->path($vPath . ':');

            if ($path) {
                $this->app->jbchecksum->verify(
                    $path,
                    $checksum,
                    $result,
                    function ($somePath) use ($vPath) {
                        if (preg_match("#^{$vPath}/#i", $somePath)) {
                            return preg_replace("#^{$vPath}/#i", '', $somePath);
                        }
                    },
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