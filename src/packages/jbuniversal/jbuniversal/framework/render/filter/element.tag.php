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
