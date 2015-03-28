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
 * Class JBCartElementEmailDownload
 */
class JBCartElementEmailDownload extends JBCartElementEmail
{
    /**
     * @param  array $params
     * @return bool
     */
    public function hasValue($params = array())
    {
        $files = $this->_getFiles();
        return !empty($files);
    }

    /**
     * @return array
     */
    protected function _getFiles()
    {
        $result = array();

        if ($downloadId = $this->config->get('download_element')) {

            $items = $this->getOrder()->getItems(true);

            foreach ($items as $item) {

                if (!$item['item']) { // Item no exists
                    continue;
                }

                if ($element = $item['item']->getElement($downloadId)) {

                    $file     = JString::trim($element->get('file'));
                    $fullPath = JPath::clean(JPATH_ROOT . '/' . $file);

                    if ($file && JFile::exists($fullPath)) {
                        $result[] = array(
                            'full'    => $fullPath,
                            'url'     => JUri::root() . $this->app->path->relative($fullPath),
                            'element' => $this->app->jbrouter->element($downloadId, $item['item']->id, 'download'),
                            'name'    => JString::trim($item->item_name),
                            'size'    => $this->app->filesystem->formatFilesize(filesize($fullPath)),
                        );
                    }

                }
            }
        }

        return $result;
    }

    /**
     * Render elements data
     * @param  AppData|array $params
     * @return null|string
     */
    public function render($params = array())
    {
        $mode = $this->config->get('download_mode', 'link');

        if ($mode == 'attach') {
            $files = $this->_getFiles();

            foreach ($files as $file) {
                $ext = JFile::getExt($file['full']);
                $this->_mailer->addAttachment($file['full'], $file['name'] . '.' . $ext);
            }

            return null; // no HTML
        }

        return parent::render($params);
    }

}
