<?php
/**
 * JBZoo App is universal Joomla CCK, application for YooTheme Zoo component
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
 * Class JBCartElementPriceDescription
 */
class JBCartElementPriceDescription extends JBCartElementPrice
{
    /**
     * @param array $params
     * @return bool
     */
    public function hasValue($params = array())
    {
        return true;
    }

    /**
     * Get elements search data
     * @return bool|string
     */
    public function getSearchData()
    {
        if ($value = $this->getValue()) {
            return $value;
        }

        return false;
    }

    /**
     * @return string|null
     */
    public function edit()
    {
        if ($layout = $this->getLayout('edit.php')) {
            return self::renderEditLayout($layout, array(
                'value' => $this->get('value', '')
            ));
        }

        return false;
    }

    /**
     * Returns data when variant changes
     * @param array $params
     * @return null
     */
    public function renderAjax($params = array())
    {
        return $this->render($params);
    }
}
