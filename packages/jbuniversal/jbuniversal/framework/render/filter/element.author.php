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
 * Class JBFilterElementAuthor
 */
class JBFilterElementAuthor extends JBFilterElement
{

    /**
     * Get author values
     * @param null $type
     * @return array
     */
    protected function _getValues($type = null)
    {
        $applicationId = (int)$this->_params->get('item_application_id', 0);

        return JBModelValues::model()->getAuthorValues($applicationId);
    }

    /**
     * @return bool
     */
    public function hasValue()
    {
        $data = $this->_getValues();
        return !empty($data);
    }

}
