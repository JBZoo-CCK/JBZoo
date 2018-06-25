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
