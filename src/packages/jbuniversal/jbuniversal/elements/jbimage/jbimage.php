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
 * Class ElementJBImage
 */
class ElementJBImage extends Element implements iSubmittable
{
    /**
     * @var JBImageHelper
     */
    protected $_jbimage = null;

    /**
     * Class constructor
     */
    public function __construct()
    {
        parent::__construct();

        $this->_jbimage = $this->app->jbimage;

        // Load language
        Factory::getLanguage()->load('com_jbzoo_elements_jbimage', $this->app->path->path('jbapp:elements') . '/jbimage', null, true);
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
        $data         = $this->data();
        $defaultImage = $this->getDefaultImage($params);

        $check = !empty($data);

        if (!$check && !$defaultImage)
        {
            return false;
        }

        return true;
    }

    /**
     * Returns the element's value.
     * @param array $params
     * @return Value
     */
    public function getValue($params = array())
    {
        return $this->data();
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
            return "__IMAGES_EXISTS__";
        }
        else
        {
            return "__IMAGES_NO_EXISTS__";
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
    public function renderFile($params = array(), $image = array())
    {
        //init params
        $params = $this->app->data->create($params);

        // init vars
        $width  = (int) $params->get('width', 0);
        $height = (int) $params->get('height', 0);
        $alt    = $title = isset($image['title']) ? $image['title'] : $this->getItem()->name;
        $url    = $imagePopup = $appendClass = $target = $rel = '';

        // get image
        if ($this->isFileExists($image['file']))
        {
            $img = $this->_jbimage->resize($image['file'], $width, $height);
        }
        else
        {
            $img = $this->getDefaultImage($params);
        }

        // select render template
        $template = $params->get('template', 'default');

        if ($template == 'link')
        {
            $url    = $image['link'];
            $rel    = $image['rel'];
            $target = (int) $image['target'] ? '_blank' : false;

        }
        elseif ($template == 'itemlink')
        {
            if ($this->getItem()->getState())
            {
                $url   = $this->app->jbrouter->externalItem($this->_item);
                $title = empty($title) ? $this->getItem()->name : $title;
            }

        }
        elseif ($template == 'popup')
        {

            $appendClass = 'jbimage-gallery';
            if ((int) $params->get('group_popup', 1))
            {
                $rel = 'jbimage-gallery-' . $this->getItem()->id;
            }

            $target = '_blank';

            $widthPopup  = (int) $params->get('width_popup');
            $heightPopup = (int) $params->get('height_popup');

            if ($img)
            {
                $url = $this->_jbimage->getUrl($image['file']);
                if ($widthPopup || $heightPopup)
                {
                    $newImg = $this->_jbimage->resize($img->orig, $widthPopup, $heightPopup);
                    $url    = $newImg->url;
                }
            }
        }

        // render layout
        if ($img && $layout = $this->getLayout('jbimage-' . $template . '.php'))
        {

            $unique = $params->get('_layout') . '_' . $this->_item->id . '_' . $this->identifier;

            return $this->renderLayout($layout, array(
                    'imageAttrs' => $this->_buildAttrs(array(
                        'class'         => 'jbimage ' . $unique,
                        'alt'           => $alt,
                        'title'         => $title,
                        'src'           => $img->url,
                        'width'         => $img->width,
                        'height'        => $img->height,
                        'data-template' => $template
                    )),
                    'linkAttrs'  => $this->_buildAttrs(array(
                        'class'  => 'jbimage-link ' . $appendClass . ' ' . $unique,
                        'title'  => $title,
                        'href'   => $url,
                        'rel'    => $rel,
                        'target' => $target,
                        'id'     => uniqid('jbimage-link-')
                    )),
                    'link'       => $url,
                    'image'      => $img
                )
            );
        }

        return null;
    }

    /**
     * @param   array  $attrs
     *
     * @return string
     */
    public function _buildAttrs(array $attrs)
    {
        return $this->app->jbhtml->buildAttrs($attrs);
    }

    /**
     * Render
     *
     * @param   array  $params
     */
    public function render($params = array())
    {
        $result = array();
        $params = $this->app->data->create($params);
        $images = $this->data();

        //For default image
        if (empty($images))
        {
            $images    = array();
            $images[0] = null;
        }

        switch ($params->get('display', 'all'))
        {
            case 'first':
                $result[] = $this->renderFile($params, $images[0]);
                break;
            case 'all_without_first':
                array_shift($images);
                foreach ($images as $image)
                {
                    $result[] = $this->renderFile($params, $image);
                }
                break;
            case 'all':
            default:
                foreach ($images as $image)
                {
                    $result[] = $this->renderFile($params, $image);
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
        $this->app->jbassets->less('elements:jbimage/assets/less/edit.less');

        $images  = $this->data();
        $id      = $this->app->jbstring->getId('jbupload-');
        $options = $this->getOptions();

        $options['trusted_mode'] = 1;

        if ($layout = $this->getLayout('edit.php'))
        {
            return $this->renderLayout($layout,
                compact('images', 'id', 'options')
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
     * Renders the element in submission.
     *
     * @param   array  $params  submission parameters
     *
     * @return null|string|void
     */
    public function renderSubmission($params = array())
    {
        $this->app->jbassets->less('elements:jbimage/assets/less/submission.less');

        $images  = $this->data();
        $id      = $this->app->jbstring->getId('jbupload-');
        $options = $this->getOptions($params);

        if ($layout = $this->getLayout('submission.php'))
        {
            return $this->renderLayout($layout,
                compact('images', 'id', 'options')
            );
        }

        return null;
    }

    /**
     * Validates the submitted element
     *
     * @param   AppData  $value   value
     * @param   AppData  $params  submission parameters
     *
     * @return array
     * @throws AppValidatorException
     */
    public function validateSubmission($value, $params)
    {
        $result       = array();
        $trusted_mode = $params->get('trusted_mode');

        foreach ($value as $key => $single_value)
        {
            try
            {

                $result[$key]['file'] = $this->app->validator->create('string', array('required' => true))->clean($single_value['file']);

                if ($trusted_mode)
                {
                    $result[$key]['title']  = $this->app->validator->create('string', array('required' => false))->clean($single_value['title']);
                    $result[$key]['link']   = $this->app->validator->create('url', array('required' => false), array('required' => 'Please enter an URL.'))->clean($single_value['link']);
                    $result[$key]['target'] = $this->app->validator->create('', array('required' => false))->clean($single_value['target']);
                    $result[$key]['rel']    = $this->app->validator->create('string', array('required' => false))->clean($single_value['rel']);
                }

            }
            catch (AppValidatorException $e)
            {

                if ($e->getCode() != AppValidator::ERROR_CODE_REQUIRED)
                {
                    throw $e;
                }
            }
        }

        if ($params->get('required') && !count($result))
        {
            if (isset($e))
            {
                throw $e;
            }
            throw new AppValidatorException('This field is required');
        }

        return $result;
    }

    /**
     * Get upload image path
     * @return string
     */
    protected function getUploadImagePath()
    {
        $item         = $this->getItem();
        $uploadBy     = $this->config->get('upload_by', '');
        $uploadByUser = (int) $this->config->get('upload_by_user', 0); // Old JBImage

        if ($uploadByUser)
        {
            $uploadBy = 'user';
        }

        $uploadDirectory = trim(trim($this->config->get('upload_directory', 'images/zoo/uploads/')), '\/');

        switch ($uploadBy)
        {
            case 'user':
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

                break;

            case 'date':
                $uploadDirectory .= '/' . date("d-m-Y");
        }

        return Path::clean($uploadDirectory);
    }

    /**
     * Get default image
     *
     * @param   JSONData  $params
     *
     * @return null|object
     */
    protected function getDefaultImage($params)
    {
        $params = $this->app->data->create($params);

        // init vars
        $width         = (int) $params->get('width', 0);
        $height        = (int) $params->get('height', 0);
        $defaultImage  = $this->config->get('default_image');
        $defaultEnable = (int) $this->config->get('default_enable', 0);

        $result = null;

        if ($defaultEnable && $defaultImage)
        {

            if (strpos($defaultImage, 'http') !== false)
            {

                return (object) array(
                    'width'   => $width,
                    'height'  => $height,
                    'path'    => $defaultImage,
                    'orig'    => $defaultImage,
                    'origUrl' => $defaultImage,
                    'url'     => $defaultImage,
                    'rel'     => $defaultImage,
                );

            }
            else
            {
                return $this->_jbimage->resize($defaultImage, $width, $height);
            }
        }

        return null;
    }

    /**
     * Is file exists
     *
     * @param   string  $imagePath
     *
     * @return bool
     */
    protected function isFileExists($imagePath)
    {
        if (strpos($imagePath, 'http') !== false)
        {
            return true;

        }
        else if (File::exists($imagePath) || File::exists(JPATH_ROOT . '/' . $imagePath))
        {
            return true;
        }


        return false;
    }

    /**
     * Get watermark
     */
    public function getWatermark()
    {
        if (!$this->config->get('watermark_enable'))
        {
            return false;
        }

        $file = trim(trim($this->config->get('watermark_path')), '\/');

        if ($this->isFileExists($file))
        {
            return $file;
        }
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
        $images = $this->data();

        foreach ($images as $key => $image)
        {
            $result[] = $this->deleteFile($image['file']);

        }

        return $result;
    }

    /**
     * Each image delete
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
            'upload'           => $this->getUploadImagePath(),
            'maxNumberOfFiles' => (int) $this->config->get('max_number', 10),
            'previewMaxWidth'  => (int) $this->config->get('thumb_width', 175),
            'previewMaxHeight' => (int) $this->config->get('thumb_height', 110),
            'maxFileSize'      => (int) $this->config->get('max_upload_size', 10000) * 1000,
            'imageMaxWidth'    => (int) $this->config->get('max_width', 1920),
            'imageMaxHeight'   => (int) $this->config->get('max_height', 1080),
            'deleteType'       => $this->config->get('delete_type', 'simple'),
            'paramName'        => $this->identifier . '-jbimages',
            'class'            => 'jbimage',
            'types'            => 'gif|jpe?g|png',
            'trusted_mode'     => $params ? (int) $params->get('trusted_mode') : 0,
            'tag'              => 'img'
        );

        if ($watermark = $this->getWatermark())
        {
            $options['watermark'] = $watermark;
        }

        return $options;
    }
}
