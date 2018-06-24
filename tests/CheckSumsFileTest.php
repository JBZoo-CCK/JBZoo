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
 */

namespace JBZoo\PHPUnit;

/**
 * Class CheckSumsFileTest
 * @package JBZoo\PHPUnit
 */
class CheckSumsFileTest extends PHPUnit
{
    protected $root = PATH_PACKAGES;

    protected static $virtualPaths = [
        'jbapp'              => 'jbuniversal/jbuniversal',
        'mod_jbzoo_basket'   => 'mod_jbzoo_basket',
        'mod_jbzoo_props'    => 'mod_jbzoo_props',
        'mod_jbzoo_search'   => 'mod_jbzoo_search',
        'mod_jbzoo_category' => 'mod_jbzoo_category',
        'mod_jbzoo_currency' => 'mod_jbzoo_currency',
        'mod_jbzoo_item'     => 'mod_jbzoo_item',
        'plugin_jbzoo'       => 'plg_sys_jbzoo',
    ];

    protected static $excludePaths = [
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
        'yml_config\.php',

        // orig
        '\.orig^'
    ];

    function testCreateFile()
    {
        isTrue($this->create());
    }

    /**
     * @param $virtualPath
     * @return null|string
     */
    function _getPath($virtualPath)
    {
        [$name, $relPath] = explode(':', $virtualPath);

        if (isset(self::$virtualPaths[$name])) {
            $path = realpath($this->root . '/' . self::$virtualPaths[$name] . '/' . $relPath);
            if (file_exists($path) || is_dir($path)) {
                return (string)$path;
            }
        }

        return null;
    }

    /**
     * @param        $path
     * @param string $prefix
     * @param bool   $recursive
     * @return array
     */
    function readDirectory($path, $prefix = '', $recursive = true)
    {
        $files = [];
        $ignore = ['.', '..', '.DS_Store', '.svn', '.git', '.gitignore', '.gitmodules', 'cgi-bin'];

        foreach (scandir($path, SCANDIR_SORT_NONE) as $file) {
            // ignore file ?
            if (in_array($file, $ignore, true)) {
                continue;
            }

            // get files
            if (is_dir($path . '/' . $file) && $recursive) {
                $files = array_merge(
                    $files,
                    $this->readDirectory($path . '/' . $file, $prefix . $file . '/', $recursive)
                );
            } else {
                $files[] = $prefix . $file;
            }
        }

        return $files;
    }

    /**
     * @param $path
     * @return null|string
     */
    protected function readFile($path)
    {
        $path = realpath($path);
        if (file_exists($path)) {
            return file_get_contents($path);
        }
        return null;
    }

    /**
     * @param string $path
     * @param string $ds
     * @return null|string|string[]
     */
    protected function cleanPath($path, $ds = DIRECTORY_SEPARATOR)
    {
        $path = trim($path);

        if (empty($path)) {
            $path = JPATH_ROOT;
        } elseif (($ds === '\\') && ($path[0] === '\\') && ($path[1] === '\\')) {
            $path = "\\" . preg_replace('#[/\\\\]+#', $ds, $path);
        } else {
            $path = preg_replace('#[/\\\\]+#', $ds, $path);
        }

        return $path;
    }

    /**
     * @param       $path
     * @param       $filename
     * @param       $prefix
     * @param array $exclude
     * @return bool|int
     */
    protected function sumCreateByPath($path, $filename, $prefix, $exclude = [])
    {
        $result = '';
        if (file_exists($filename)) {
            $result = $this->readFile($filename);
        } else {
            file_put_contents($filename, '');
        }

        $files = $this->readDirectory($this->cleanPath($path));

        if (\is_array($files)) {
            foreach ($files as $file) {
                $fileClean = $this->cleanPath($file);
                foreach ($exclude as $pattern) {
                    if (preg_match('#' . $pattern . '#ius', $fileClean)) {
                        continue 2;
                    }
                }

                $hash = $this->getHash("{$path}/{$file}");
                $result .= "{$hash} {$prefix}/$file\n";
            }

            return file_put_contents($filename, $result);
        }

        return false;
    }

    /**
     * Create
     */
    protected function create()
    {
        if ($checksums = $this->_getPath('jbapp:checksums')) {
            unlink($checksums);
        }

        foreach (self::$virtualPaths as $vPath => $real) {
            if ($path = $this->_getPath($vPath . ':')) {
                $this->sumCreateByPath($path, $checksums, $vPath, self::$excludePaths);
            }
        }

        return true;
    }

    /**
     * @param $filePath
     * @return string
     */
    private function getHash($filePath)
    {
        $contents = file_get_contents($filePath);

        $code = str_replace(["\r\n", "\r", "\n"], '_LE_', $contents);
        return md5($code);
    }
}
