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
 * Class JBChecksumHelper
 */
class JBChecksumHelper extends AppHelper
{

    const HASH_MODE = 1;

    /**
     * Verify a file checksum
     * @param string $path     The path to the files
     * @param string $checksum The checksum file
     * @param array  $log      Log Array
     * @param array  $filter   An array of filter functions
     * @param string $prefix   A prefix for the file
     * @param array  $exclude  patterns for no check
     * @return boolean If the checksum was valid
     */
    public function verify($path, $checksum, &$log = null, array $filter = array(), $prefix = '', $exclude = array())
    {
        $path = rtrim(str_replace(DIRECTORY_SEPARATOR, '/', $path), '/') . '/';

        if ($rows = file($checksum)) {

            $checksum_files = array();
            foreach ($rows as $row) {
                list($hash, $file) = explode(' ', trim($row), 2);

                foreach ($filter as $callback) {
                    if ($callback && !($file = call_user_func($callback, $file))) {
                        continue 2;
                    }

                    if ($file == 'checksums') {
                        continue 2;
                    }

                }

                $checksum_files[] = $file;

                if (!file_exists($path . $file)) {
                    $log['missing'][] = str_replace('//', '/', $prefix . '/' . $file);
                } elseif ($this->_hash($path . $file) != $hash) {
                    $log['modified'][] = str_replace('//', '/', $prefix . '/' . $file);
                }
            }

            foreach ($this->_readDirectory($path) as $file) {
                if (
                    !in_array($file, $checksum_files) &&
                    !preg_match('/' . preg_quote($file, '/') . '$/i', $checksum) &&
                    !$this->_isIgnore($file, $exclude)
                ) {
                    $log['unknown'][] = str_replace('//', '/', $prefix . '/' . $file);
                }
            }

        }

        return empty($log);
    }

    /**
     * Check is path is ignore
     * @param       $filename
     * @param array $exclude
     * @return bool
     */
    protected function _isIgnore($filename, $exclude = array())
    {
        $filename = JPath::clean($filename);

        foreach ($exclude as $pattern) {
            if (preg_match('#' . $pattern . '#ius', $filename)) {
                //echo '#' . $pattern . '#ius', "&nbsp;&nbsp;&nbsp;&nbsp;=&nbsp;&nbsp;&nbsp;&nbsp;",  $filename . '<br>';
                return true;
            }
        }
        return false;
    }

    /**
     * Read the files from a directory
     * @param string  $path      The path in which to search
     * @param string  $prefix    File prefix
     * @param boolean $recursive If the scan should be recursive (default: true)
     * @return array The file list
     * @since 1.0.0
     */
    protected function _readDirectory($path, $prefix = '', $recursive = true)
    {

        $files  = array();
        $ignore = array('.', '..', '.DS_Store', '.svn', '.git', '.gitignore', '.gitmodules', 'cgi-bin');

        foreach (scandir($path) as $file) {

            // ignore file ?
            if (in_array($file, $ignore)) {
                continue;
            }

            // get files
            if (is_dir($path . '/' . $file) && $recursive) {
                $files = array_merge($files, $this->_readDirectory($path . '/' . $file, $prefix . $file . '/', $recursive));
            } else {
                $files[] = $prefix . $file;
            }
        }

        return $files;
    }

    /**
     * @param $filePath
     * @return string
     */
    protected function _hash($filePath)
    {
        if (self::HASH_MODE) {
            $code = $this->app->jbfile->read($filePath, true);
            $code = str_replace(array("\r\n", "\r", "\n"), "_LE_", $code);
            $hash = md5($code);
        } else {
            $hash = md5_file($filePath);
        }

        return $hash;
    }

}
