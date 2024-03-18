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

use Joomla\CMS\Factory;
use Joomla\CMS\Filesystem\File;
use Joomla\CMS\Filesystem\Path;

/**
 * Class ElementJBVideo
 */
class ElementJBVideo extends Element
{
    /**
     * Class constructor
     */
    public function __construct()
    {
        parent::__construct();

        // Load language
        Factory::getLanguage()->load('com_jbzoo_elements_jbvideo', $this->app->path->path('jbapp:elements') . '/jbvideo', null, true);
    }

    /**
     * Checks if the repeatables element's value is set.
     *
     * @param   array  $params  render parameter
     *
     * @return bool true, on success
     */
    public function hasValue($params = array())
    {
        $data = $this->data();

        $check = !empty($data);

        if (!$check)
        {
            return false;
        }

        return true;
    }

    /**
     * Get elements search data.
     * @return string Search data
     */
    public function getSearchData()
    {
        $data  = $this->data();
        $check = !empty($data);

        if ($check)
        {
            return "__VIDEOS_EXISTS__";
        }
        else
        {
            return "__VIDEOS_NO_EXISTS__";
        }
    }

    public function getControlName($name, $index = 0)
    {
        return "elements[$this->identifier][$index][$name]";
    }

    /**
     * Renders the element.
     *
     * @param   array  $params  render parameter
     *
     * @return null|string HTML
     */
    public function renderFile($params = array(), $video = array())
    {
        //init params
        $params = $this->app->data->create($params);

        // init vars
        $file = $video['file'];

        // select render template
        $template = $params->get('template', 'default');

        // render layout
        if ($file && $layout = $this->getLayout('default.php'))
        {
            return $this->renderLayout($layout, array(
                    'file' => $file
                )
            );
        }

        return null;
    }

    /**
     * Render
     *
     * @param   array  $params
     */
    public function render($params = array())
    {
        // Video JS
        $this->app->jbassets->js('elements:jbvideo/assets/js/video.min.js');
        $this->app->jbassets->css('elements:jbvideo/assets/css/video-js.css');
        $this->app->jbassets->css('elements:jbvideo/assets/css/video-js.min.css');

        $params = $this->app->data->create($params);
        $result = array();
        $videos = $this->data();

        switch ($params->get('display', 'all'))
        {
            case 'first':
                $result[] = $this->renderFile($params, $videos[0]);
                break;
            case 'all_without_first':
                array_shift($videos);
                foreach ($videos as $video)
                {
                    $result[] = $this->renderFile($params, $video);
                }
                break;
            case 'all':
            default:
                foreach ($videos as $video)
                {
                    $result[] = $this->renderFile($params, $video);
                }
                break;
        }

        return $this->app->element->applySeparators($params->get('separated_by'), $result);
    }

    /**
     * Renders the edit form field.
     * @return string HTML
     */
    public function edit()
    {
        $this->app->jbassets->less('elements:jbvideo/assets/less/edit.less');

        $videos = $this->data();
        $id      = $this->app->jbstring->getId('jbupload-');
        $options = $this->getOptions();

        if ($layout = $this->getLayout('edit.php'))
        {
            return $this->renderLayout($layout,
                compact('videos', 'id', 'options')
            );
        }
    }

    /*
        Function: bindData
            Set data through data array.

        Parameters:
            $data - array

        Returns:
            Void
    */
    public function bindData($data = array())
    {
        if (!empty($data))
        {
            $data = array_values($data);
        }

        parent::bindData($data);
    }

    /**
     * Get upload video path
     * @return string
     */
    protected function getUploadVideoPath()
    {
        $item            = $this->getItem();
        $uploadByUser    = (int) $this->config->get('upload_by_user', 0);
        $uploadByDate    = (int) $this->config->get('upload_by_date', 0);
        $uploadDirectory = trim(trim($this->config->get('upload_directory', 'images/zoo/uploads/')), '\/');

        if ($uploadByUser)
        {
            if ($item->id)
            {
                $user_id = ($item->created_by) ? $item->created_by : 'quest';
            }
            else
            {
                $user    = Factory::getUser();
                $user_id = $user->id;
            }

            $uploadDirectory .= '/' . $user_id;

            return Path::clean($uploadDirectory);
        }

        if ($uploadByDate)
        {
            $uploadDirectory .= '/' . date("d-m-Y");

            return Path::clean($uploadDirectory);
        }

        return Path::clean($uploadDirectory);
    }

    /**
     * Is file exists
     *
     * @param   string  $imagePath
     *
     * @return bool
     */
    protected function isFileExists($path)
    {
        if (strpos($path, 'http') !== false)
        {
            return true;

        }
        else if (File::exists($path) || File::exists(JPATH_ROOT . '/' . $path))
        {
            return true;
        }


        return false;
    }

    /**
     * Trigger on item delete
     */
    public function triggerItemDeleted()
    {
        $removeWithItem = (int) $this->config->get('remove_with_item');

        if (!$removeWithItem)
        {
            return null;
        }

        $result = array();
        $videos = $this->data();

        foreach ($videos as $key => $video)
        {
            $result[] = $this->deleteFile($video['file']);

        }

        return $result;
    }

    /**
     * Each video delete
     */
    protected function deleteFile($file)
    {
        if (File::exists(JPATH_ROOT . '/' . $file))
        {
            return File::delete(JPATH_ROOT . '/' . $file);
        }

        return null;
    }

    // Get options

    public function getOptions($params = array())
    {
        $options = array(
            'id'               => $this->identifier,
            'url'              => $this->app->jbrouter->elementsUpload(),
            'upload'           => $this->getUploadVideoPath(),
            'maxNumberOfFiles' => (int) $this->config->get('max_number', 10),
            'maxFileSize'      => (int) $this->config->get('max_upload_size', 10000) * 1000,
            'deleteType'       => $this->config->get('delete_type', 'simple'),
            'paramName'        => $this->identifier . '-jbvideos',
            'class'            => 'jbvideo',
            'types'            => 'mp4|mpeg|webm|flv|swf',
            'trusted_mode'     => false,
            'tag'              => 'video'
        );

        return $options;
    }
}
