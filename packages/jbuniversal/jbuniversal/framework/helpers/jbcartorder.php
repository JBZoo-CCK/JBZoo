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
 * Class JBCartOrderHelper
 */
class JBCartOrderHelper extends AppHelper
{
    /**
     * @return array
     */
    public function getExistsUsers()
    {
        $rows = JBModelOrder::model()->getUserList();

        $result = array();

        if (!empty($rows)) {
            foreach ($rows as $row) {

                if ($row->user_id > 0) {
                    $result[$row->user_id] = $row->user_name;
                } else {
                    $result[$row->created_by] = 'undefined id:' . $row->created_by;
                }
            }
        }

        return $result;
    }
}
