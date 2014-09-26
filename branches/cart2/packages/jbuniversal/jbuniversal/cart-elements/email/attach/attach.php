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
 * Class JBCartElementEmailAttach
 */
class JBCartElementEmailAttach extends JBCartElementEmail
{
    protected $_extensions = '';

    /**
     * Class constructor
     *
     * @param App    $app
     * @param string $type
     * @param string $group
     */
    public function __construct($app, $type, $group)
    {
        parent::__construct($app, $type, $group);

        $this->registerCallback('files');
    }

    /**
     * Check elements value.
     * Output element or no.
     *
     * @param  array $params
     *
     * @return bool
     */
    public function hasValue($params = array())
    {
        $file = $this->config->get('file');
        if (!empty($file)) {
            return true;
        }

        return false;
    }

    /**
     * Render elements data
     *
     * @param  array $params
     *
     * @return null|string
     */
    public function render($params = array())
    {
        if ($layout = $this->getLayout($params->get('_layout') . '.php')) {
            return self::renderLayout($layout, array(
                'params' => $params,
                'order'  => $this->getOrder()
            ));
        }

        return false;
    }

    /**
     * @return bool
     */
    public function getFile()
    {
        $file = JString::trim($this->config->get('file'));
        if (!empty($file)) {
            return $this->app->path->path('root:' . $file);
        }

        return false;
    }

    /**
     * Load elements css/js assets
     * @return $this
     */
    public function loadConfigAssets()
    {
        parent::loadConfigAssets();

        $this->app->document->addScript('assets:js/finder.js');
        $this->app->document->addScript('cart-elements:email/attach/js/finder.js');
        $this->app->document->addStylesheet('assets:css/ui.css');

        return $this;
    }
}
