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
 * Class JBCartElementOrderMigration
 */
class JBCartElementOrderMigration extends JBCartElementOrder
{
    /**
     * Renders the element in submission
     * @param array $params
     * @return string
     */
    public function renderSubmission($params = array())
    {
        return 'This is deprecated field and you shouldn\'t use it.';
    }

}
