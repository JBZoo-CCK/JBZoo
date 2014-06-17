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
 * Class JBModelApp
 */
class JBModelApp extends JBModel
{

    /**
     * Create and return self instance
     * @return JBModelApp
     */
    public static function model()
    {
        return new self();
    }

    /**
     * Get application list
     * @return array
     */
    public function getList()
    {
        return $this->app->table->application->all(array(
            'conditions' => array(
                'application_group = "' . JBZOO_APP_GROUP . '"'
            )
        ));
    }

    /**
     * Get simple application list
     */
    public function getSimpleList()
    {
        $select = $this->_getSelect()
            ->select('id, alias, name, application_group')
            ->from(ZOO_TABLE_APPLICATION)
            ->order('name');

        return $this->_query($select);
    }
}