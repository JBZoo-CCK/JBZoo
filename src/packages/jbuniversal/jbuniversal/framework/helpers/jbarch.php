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
 * Class JBArchHelper
 */
class JBArchHelper extends AppHelper
{

    /**
     * Compress files by path
     * @param $path
     * @param $archName
     * @param array $options
     * @return null|string
     */
    public function compress($path, $archName, $options = array())
    {
        $filename = $this->app->jbpath->sysPath('tmp', $archName . '.zip');
        $zip      = $this->app->archive->open($filename, 'zip');

        if (is_array($path)) {
            $fileList = $path;

        } else {
            if (JFolder::exists($path)) {
                $fileList = JFolder::files($path, '.', true, true);

            } else if (JFile::exists($path)) {
                $fileList = array($path);
            }
        }

        if (!empty($fileList)) {

            if (isset($options['remove-path'])) {
                $zip->create($fileList, PCLZIP_OPT_REMOVE_PATH, $options['remove-path']);
            } else {
                $zip->create($fileList, PCLZIP_OPT_REMOVE_ALL_PATH);
            }

            return $filename;
        }

        return null;
    }

}
