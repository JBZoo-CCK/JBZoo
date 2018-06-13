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
 * Class JBCartElementEmailAttach
 */
class JBCartElementEmailAttach extends JBCartElementEmail
{

    /**
     * Check elements value.
     * @param  array $params
     * @return bool
     */
    public function hasValue($params = array())
    {
        return $this->_getFile();
    }

    /**
     * Render elements data
     * @param  array $params
     * @return null|string
     */
    public function render($params = array())
    {
        $file = $this->_getFile();
        $name = $this->getName() . '.' . JFile::getExt($file);

        $this->_mailer->addAttachment($file, $name);
    }

    /**
     * @return string|null
     */
    protected function _getFile()
    {
        $file = $this->config->get('file');
        $file = JString::trim($file);

        if (JFile::exists($file)) {
            return $file;
        }
    }

    /**
     * Load elements css/js assets
     * @return $this
     */
    public function loadConfigAssets()
    {
        parent::loadConfigAssets();

        $this->app->jbassets->js('assets:js/finder.js');
        $this->app->jbassets->js('cart-elements:email/attach/assets/js/attach.js');
        $this->app->jbassets->css('assets:css/ui.css');

        return $this;
    }

}
