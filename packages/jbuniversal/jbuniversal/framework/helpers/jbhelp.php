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
 * Class JBHelpHelper
 */
class JBHelpHelper extends AppHelper
{

    /**
     * Hook jbzoo help information file
     * @param string $controller
     * @param string $position
     * @return null|string
     */
    public function hook($controller = 'cart', $position = 'top')
    {
        if ($task = $this->app->jbrequest->get('task')) {

            $file     = $this->_getName($task . '_' . $position);
            $filePath = $this->app->path->path('jbviews:jb' . $controller . '/help/' . $file);

            if (JFile::exists($filePath)) {
                ob_start();
                include($filePath);
                $output = ob_get_contents();
                ob_end_clean();
                return $output;
            }
        }

        return null;
    }

    /**
     * Get file name
     * @param $name
     * @return mixed|string
     */
    protected function _getName($name)
    {
        $file = preg_replace('/[^A-Z0-9_\.-]/i', '', $name);
        $ext  = pathinfo($file, PATHINFO_EXTENSION);

        if (empty($ext)) {
            $file .= '.php';
        }

        return JString::strtolower($file);
    }

}
