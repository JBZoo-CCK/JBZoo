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
 * Class JBCartElementPriceBool
 */
class JBCartElementPriceBool extends JBCartElementPrice
{
    /**
     * Check if element has value
     *
     * @param array $params
     *
     * @return bool
     */
    public function hasValue($params = array())
    {
        $value = $this->getOptions();

        if (!empty($value)) {
            return true;
        }

        return false;
    }

    /**
     * Get elements search data
     * @return mixed
     */
    public function getSearchData()
    {
        $value = $this->getValue();

        return $value;
    }

    /**
     * @return mixed|null|string
     */
    public function edit()
    {
        if ($layout = $this->getLayout('edit.php')) {
            return self::renderLayout($layout);
        }

        return null;
    }

    /**
     * @param array $params
     *
     * @return array|mixed|null|string
     */
    public function render($params = array())
    {
        $data   = array(
            JText::_('JBZOO_NO')  => JText::_('JBZOO_NO'),
            JText::_('JBZOO_YES') => JText::_('JBZOO_YES')
        );

        if ($layout = $this->getLayout('radio.php')) {
            return self::renderLayout($layout, array(
                'data'   => $this->getOptions($data),
                'params' => $params
            ));
        }

        return null;
    }

}
