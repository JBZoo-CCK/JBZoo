<?php
/**
 * JBZoo App is universal Joomla CCK, application for YooTheme Zoo component
 *
 * @package     jbzoo
 * @version     2.x Pro
 * @author      JBZoo App http://jbzoo.com
 * @copyright   Copyright (C) JBZoo.com,  All rights reserved.
 * @license     http://jbzoo.com/license-pro.php JBZoo Licence
 * @coder       Alexander Oganov <t_tapak@yahoo.com>
 */

// no direct access
defined('_JEXEC') or die('Restricted access');


/**
 * Class ViewedJBUniversalController
 */
class ViewedJBUniversalController extends JBUniversalController
{

    /**
     * Method to clear recently viewed history
     * @return array | boolean $result
     */
    public function clear()
    {
        $result = $this->app->jbviewed->clear();
        $this->app->jbajax->send(array(), $result);
    }
}