<?php
/**
 * JBZoo App is universal Joomla CCK, application for YooTheme Zoo component
 *
 * @package     jbzoo
 * @version     2.x Pro
 * @author      JBZoo App http://jbzoo.com
 * @copyright   Copyright (C) JBZoo.com,  All rights reserved.
 * @license     http://jbzoo.com/license-pro.php JBZoo Licence
 * @coder       Segrey Kalistratov <kalistratov.s.m@gmail.com>
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
