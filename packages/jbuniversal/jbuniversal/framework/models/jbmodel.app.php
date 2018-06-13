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