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
 * Class JBCartElementEmailItems
 */
class JBCartElementEmailItems extends JBCartElementEmail
{
    /**
     * Check elements value
     * @param  array $params
     * @return bool
     */
    public function hasValue($params = array())
    {
        $items = $this->getOrder()->getItems(false);
        if (!empty($items)) {
            return true;
        }

        return false;
    }

    /**
     * Add image width CID to attach. Use for Order Items.
     * @param string $path Path to image
     * @param string $cid  Conten-ID
     */
    protected function _addEmailImage($path, $cid)
    {
        $name = $cid . '.' . JFile::getExt($path);
        $this->_mailer->AddEmbeddedImage($path, $cid, $name);
    }

    /**
     * @return mixed
     */
    protected function _getCurrency()
    {
        return $this->config->get('currency');
    }

}
