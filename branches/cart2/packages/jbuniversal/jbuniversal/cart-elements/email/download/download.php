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
 * Class JBCartElementEmailDownload
 */
class JBCartElementEmailDownload extends JBCartElementEmail
{
    const MODE_ATTACH = 'attach';

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
        $order = $this->getOrder();
        $field = $params->get('element', false);

        if ($order->id && $field) {
            return true;
        }

        return false;
    }

    /**
     * Render elements data
     * @param  AppData|array $params
     * @return null|string
     */
    public function render($params = array())
    {
        $order = $this->getSubject();
        $items = $order->getItems(true);

        if ($layout = $this->getLayout('order.php')) {
            return self::renderLayout($layout, array(
                'items'         => $items,
                'mode'          => (int)$params->get('mode', 1),
                'size'          => (int)$params->get('file_size', 1),
                'download_name' => $params->get('download_name', ''),
                'identifier'    => $params->get('element', false)
            ));
        }

        return false;
    }

    /**
     * Add attachment to JMail
     * @return $this
     */
    public function addAttachment()
    {
        $order = $this->getSubject();
        $items = $order->getItems(true);

        if (!empty($items)) {
            foreach ($items as $key => $data) {
                if ($file = $this->getFile($data)) {
                    $this->_attach($file, ucfirst($data->get('item_name')) . ' - ' . self::filename($file));
                }
            }
        }

        return $this;
    }

    /**
     * Get file from related element
     * @param array $data Item data
     * @return bool
     */
    public function getFile($data = array())
    {
        $file = false;
        if ($element = $this->getElement($data)) {
            $file = $element->get('file');
        }

        return $file;
    }

    /**
     * Get related element
     * @param array $data
     * @return Element
     */
    public function getElement($data = array())
    {
        /** @type Item $item */
        $item = $data->get('item');
        if (!$item instanceof Item) {
            $item = $this->app->table->item->get($data->get('item_id'));
        }

        return $item->getElement($this->config->get('element'));
    }
}
