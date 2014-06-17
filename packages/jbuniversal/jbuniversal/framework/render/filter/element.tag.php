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
 * Class JBFilterElementTag
 */
class JBFilterElementTag extends JBFilterElement
{
    /**
     * Get tags values
     * @param null $type
     * @return array
     */
    protected function _getValues($type = null)
    {
        $applicationId = (int)$this->_params->get('item_application_id', 0);
        $itemType      = $this->_params->get('item_type', null);

        return JBModelValues::model()->getTagValues($applicationId, $itemType);
    }
}
