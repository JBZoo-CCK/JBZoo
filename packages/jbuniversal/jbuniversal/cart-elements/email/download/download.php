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
