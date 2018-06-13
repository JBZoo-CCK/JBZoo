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
