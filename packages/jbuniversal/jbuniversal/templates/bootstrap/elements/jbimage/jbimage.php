<?php
/**
 * JBZoo App is universal Joomla CCK, application for YooTheme Zoo component
 *
 * @package     jbzoo
 * @version     2.x Pro
 * @author      JBZoo App http://jbzoo.com
 * @copyright   Copyright (C) JBZoo.com,  All rights reserved.
 * @license     http://jbzoo.com/license-pro.php JBZoo Licence
 * @coder       Denis Smetannikov <denis@jbzoo.com>
 * @coder       Sergey Kalistratov <denis@jbzoo.com>
 */

// no direct access
defined('_JEXEC') or die('Restricted access');


// register ElementRepeatable class
App::getInstance('zoo')->loader->register('ElementRepeatable', 'elements:repeatable/repeatable.php');

/**
 * Class ElementJBImage
 */
class ElementJBImage extends ElementRepeatable implements iRepeatSubmittable
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
    }

    /**
     * Checks if the repeatables element's value is set.
     *
     * @param array $params render parameter
     *
     * @return bool true, on success
     */
    public function _hasValue($params = array())
    {
        $file         = JString::trim($this->get('file'));
        $isExists     = !empty($file) && JFile::exists(JPATH_ROOT . '/' . $file);
        $defaultImage = $this->_getDefaultImage($params);

        if (!$isExists && !$defaultImage) {
            return false;
        }

        return true;
    }

    /**
     * Get elements search data.
     * @return string Search data
     */
    public function _getSearchData()
    {
        $file     = JString::trim($this->get('file'));
        $isExists = !empty($file) && JFile::exists(JPATH_ROOT . '/' . $file);
        $title    = $this->get('title');

        if ($isExists) {
            return $title . "\n" . JBModelElementJBImage::IMAGE_EXISTS;
        } else {
            return $title . "\n__" . JBModelElementJBImage::IMAGE_NO_EXISTS;
        }
    }

    /**
     * @return null|string
     */
    public function getSearchData()
    {
        $result = array();
        foreach ($this as $self) {
            $result[] = $this->_getSearchData();
        }

        return (empty($result) ? null : implode(PHP_EOL, $result));
    }

    /**
     * Renders the element.
     *
     * @param array $params render parameter
     *
     * @return null|string HTML
     */
    public function _render($params = array())
    {
        //init params
        $params = $this->app->data->create($params);

        // init vars
        $title  = $this->get('title');
        $width  = (int)$params->get('width', 0);
        $height = (int)$params->get('height', 0);
        $alt    = $title = empty($title) ? $this->getItem()->name : $title;
        $url    = $imagePopup = $appendClass = $target = $rel = '';
        $imgClass = 'jbimage';

        // get image
        if ($this->_isFileExists($this->get('file'))) {
            $image = $this->_jbimage->resize($this->get('file'), $width, $height);
        } else {
            $image = $this->_getDefaultImage($params);
        }

        // select render template
        $template = $params->get('template', 'default');

        if ($template == 'link') {
            $url    = $this->get('link');
            $rel    = $this->get('rel');
            $target = (int)$this->get('target') ? '_blank' : false;
            $appendClass = ' uk-thumbnail uk-overlay';
        } elseif ($template == 'itemlink') {
            if ($this->getItem()->getState()) {
                $url   = $this->app->jbrouter->externalItem($this->_item);
                $title = empty($title) ? $this->getItem()->name : $title;
            }

            $appendClass = ' uk-thumbnail uk-overlay';

        } elseif ($template == 'popup') {

            $appendClass = 'jbimage-gallery uk-thumbnail uk-overlay';
            if ((int)$params->get('group_popup', 1)) {
                $rel = 'jbimage-gallery-' . $this->getItem()->id;
            }

            $target = '_blank';

            $widthPopup  = (int)$params->get('width_popup');
            $heightPopup = (int)$params->get('height_popup');

            if ($image) {
                $url = $this->_jbimage->getUrl($this->get('file'));
                if ($widthPopup || $heightPopup) {
                    $newImg = $this->_jbimage->resize($image->orig, $widthPopup, $heightPopup);
                    $url    = $newImg->url;
                }
            }

        } elseif ($template == 'default') {
            $imgClass .= ' uk-thumbnail';
        }

        // render layout
        if ($image && $layout = $this->getLayout('jbimage-' . $template . '.php')) {

            $unique = $params->get('_layout') . '_' . $this->_item->id . '_' . $this->identifier;

            return $this->renderLayout($layout, array(
                    'imageAttrs' => $this->_buildAttrs(array(
                            'class'         => $imgClass . ' ' . $unique,
                            'alt'           => $alt,
                            'title'         => $title,
                            'src'           => $image->url,
                            'width'         => $image->width,
                            'height'        => $image->height,
                            'data-template' => $template
                        )),
                    'linkAttrs'  => $this->_buildAttrs(array(
                            'class'  => 'jbimage-link ' . $appendClass . ' ' . $unique,
                            'title'  => $title,
                            'href'   => $url,
                            'rel'    => $rel,
                            'target' => $target,
                            'id'     => uniqid('jbimage-link-'),
                        )),
                    'link'       => $url,
                    'image'      => $image
                )
            );
        }

        return null;
    }

    /**
     * Render
     *
     * @param array $params
     */
    public function render($params = array())
    {
        $result = array();
        $params = $this->app->data->create($params);

        switch ($params->get('display', 'all')) {
            case 'first':
                $this->seek(0);
                $result[] = $this->_render($params);
                break;
            case 'all_without_first':
                $this->seek(1);
                while ($this->valid()) {
                    $result[] = $this->_render($params);
                    $this->next();
                }
                break;
            case 'all':
            default:
                foreach ($this as $self) {
                    $result[] = $this->_render($params);
                }
                break;
        }

        return $this->app->element->applySeparators($params->get('separated_by'), $result);
    }

    /**
     * @param array $attrs
     *
     * @return string
     */
    public function _buildAttrs(array $attrs)
    {
        return $this->app->jbhtml->buildAttrs($attrs);
    }

    /**
     * Renders the edit form field.
     * @return string HTML
     */
    public function _edit()
    {
        $this->app->document->addScript('elements:jbimage/assets/js/edit.js');

        if ($layout = $this->getLayout('_editrow.php')) {
            return $this->renderLayout($layout);
        }

        return null;
    }

    /**
     * Renders the element in submission.
     *
     * @param array $params submission parameters
     *
     * @return null|string|void
     */
    public function _renderSubmission($params = array())
    {
        $this->app->jbsession->set($this->identifier . '||' . $this->key(), null, 'jbimage_validate');

        $this->app->document->addScript('elements:jbimage/assets/js/submission.js');
        $this->app->jbassets->addRootUrl();

        // init vars
        $data  = $this->data();
        $image = $this->get('file');
        if (isset($data[$this->key()]['image'])) {
            $image = $data[$this->key()]['image'];
        }

        // is uploaded file
        $image = is_array($image) ? '' : $image;

        // get params
        $trusted_mode = $params->get('trusted_mode');

        // build image select
        $lists = array();
        if ($trusted_mode) {

            $options = array($this->app->html->_('select.option', '', '- ' . JText::_('Select Image') . ' -'));

            if (!empty($image) && $this->_inUploadPath($image)) {
                $options[] = $this->app->html->_('select.option', $image, '- ' . JText::_('No Change') . ' -');
            }

            $img_ext =
                str_replace(',', '|', trim(JComponentHelper::getParams('com_media')->get('image_extensions'), ','));
            foreach ($this->app->path->files('root:' . $this->_getUploadImagePath(), false,
                '/\.(' . $img_ext . ')$/i') as $file) {
                $options[] = $this->app->html->_('select.option', $this->_getUploadImagePath() . '/' . $file, $file);
            }

            $lists['image_select'] =
                $this->app->html->_('select.genericlist', $options, $this->getControlName('image'), 'class="image"',
                    'value', 'text', $image);

        } else {
            if (!empty($image)) {
                $image = $this->app->zoo->resizeImage($this->app->path->path('root:' . $image), 0, 0);
                $image = $this->app->path->relative($image);
            }
        }

        if (!empty($image)) {
            $image = $this->app->path->url('root:' . $image);
        }

        if ($layout = $this->getLayout('submission.php')) {
            return $this->renderLayout($layout,
                compact('lists', 'image', 'trusted_mode')
            );
        }

        return null;
    }

    /**
     * Validates the submitted element
     *
     * @param AppData $value  value
     * @param AppData $params submission parameters
     *
     * @return array
     * @throws AppValidatorException
     */
    public function _validateSubmission($value, $params)
    {
        // init vars
        $trusted_mode = $params->get('trusted_mode');

        // get old file value
        $old_file = $this->get('file');

        $file = '';

        // get file from select list
        if ($trusted_mode && $file = $value->get('image')) {

            if (!$this->_inUploadPath($file) && $file != $old_file) {
                throw new AppValidatorException(sprintf('This file is not located in the upload directory.'));
            }

            if (!JFile::exists($file)) {
                throw new AppValidatorException(sprintf('This file does not exist.'));
            }

        } else {

            try {
                // get the uploaded file information
                $userfile = $this->_getUploadedFile();

                // validator hack for element error message after submission controller redirect
                if (
                    (empty($userfile) || empty($userfile['tmp_name']))
                    && ($value->get('filename') || $value->get('image'))
                ) {
                    if (
                    $message = $this->app->jbsession->get($this->identifier . '||' . $this->key(), 'jbimage_validate')
                    ) {
                        throw new AppValidatorException($message);
                    }
                }

                // hack hide undefined error after redirect
                if (!empty($userfile)) {
                    $max_upload_size = $this->config->get('max_upload_size', '512') * 1024;
                    $max_upload_size = empty($max_upload_size) ? null : $max_upload_size;

                    $file = $this->app->validator
                        ->create('file', array('mime_type_group' => 'image', 'max_size' => $max_upload_size))
                        ->addMessage('mime_type_group', 'Uploaded file is not an image.')
                        ->clean($userfile);
                }

            } catch (AppValidatorException $e) {

                $this->app->jbsession->set($this->identifier . '||' . $this->key(), $e->getMessage(),
                    'jbimage_validate');

                if ($e->getCode() != UPLOAD_ERR_NO_FILE) {
                    throw $e;
                }

                if (!$trusted_mode && $old_file && $value->get('image')) {
                    $file = $old_file;
                }
            }
        }

        if ($params->get('required') && empty($file)) {
            throw new AppValidatorException('Please select an image to upload.');
        }

        $result = array('file' => $this->_moveUploadedFiles($file));

        if ($trusted_mode) {
            $result['title']  =
                $this->app->validator->create('string', array('required' => false))->clean($value->get('title'));
            $result['link']   = $this->app->validator->create('url', array('required' => false),
                array('required' => 'Please enter an URL.'))->clean($value->get('link'));
            $result['target'] =
                $this->app->validator->create('', array('required' => false))->clean($value->get('target'));
            $result['rel']    =
                $this->app->validator->create('string', array('required' => false))->clean($value->get('rel'));
        }

        $this->next();

        return $result;
    }

    /**
     * Check is in upload path
     *
     * @param $image
     *
     * @return bool
     */
    protected function _inUploadPath($image)
    {
        return JPath::clean($this->_getUploadImagePath()) == JPath::clean(dirname($image));
    }

    /**
     * Get upload image path
     * @return string
     */
    protected function _getUploadImagePath()
    {
        $uploadByUser    = (int)$this->config->get('upload_by_user', 0);
        $uploadDirectory = trim(trim($this->config->get('upload_directory', 'images/zoo/uploads/')), '\/');

        if ($uploadByUser) {
            $user = JFactory::getUser();
            $uploadDirectory .= '/user_' . $user->id;
        }

        $uploadDirectory = JPath::clean($uploadDirectory);
        if (!JFolder::exists($uploadDirectory)) {
            mkdir($uploadDirectory, 0777, true);
            @chmod($uploadDirectory, 0777);
        }

        return $uploadDirectory;
    }

    /**
     * Callback before item submission is saved
     *
     * @param array $userfile
     *
     * @return null
     * @throws AppException
     */
    protected function _moveUploadedFiles($userfile)
    {
        if (is_array($userfile) && $userfile['size'] > 0) {
            $ext      = $this->app->filesystem->getExtension($userfile['name']);
            $basePath = JPATH_ROOT . '/' . $this->_getUploadImagePath() . '/';
            $file     = $basePath . $userfile['name'];
            $filename = basename($file, '.' . $ext);

            $i = 1;
            while (JFile::exists($file)) {
                $file = $basePath . $filename . '-' . $i++ . '.' . $ext;
            }

            if (!JFile::upload($userfile['tmp_name'], $file)) {
                throw new AppException('Unable to upload file.');
            }

            $this->app->zoo->putIndexFile(dirname($file));

            return JPath::clean($this->app->path->relative($file));
        }

        return $userfile;
    }

    /**
     * Get uploaded file for this key
     * @return array|null
     */
    protected function _getUploadedFile()
    {
        if (isset($_FILES['elements'])) {
            $values = $_FILES['elements'];

            $result = array();
            foreach ($values as $key => $value) {
                if (isset($value[$this->identifier][$this->key()]['file'])) {
                    $result[$key] = $value[$this->identifier][$this->key()]['file'];
                }
            }

            if (!empty($result)) {
                // transliteration filename to latin
                $ext            = $this->app->filesystem->getExtension($result['name']);
                $filename       = preg_replace('#\.' . $ext . '$#iu', '', $result['name']);
                $result['name'] = $this->app->string->sluggify($filename) . '.' . $ext;

                return $result;
            }
        }

        return null;
    }

    /**
     * Get default image
     *
     * @param JSONData $params
     *
     * @return null|object
     */
    protected function _getDefaultImage($params)
    {
        $params = $this->app->data->create($params);

        // init vars
        $width         = (int)$params->get('width', 0);
        $height        = (int)$params->get('height', 0);
        $defaultImage  = $this->config->get('default_image');
        $defaultEnable = (int)$this->config->get('default_enable', 0);

        $result = null;

        if ($defaultEnable && $defaultImage) {

            if (strpos($defaultImage, 'http') !== false) {

                return (object)array(
                    'width'   => $width,
                    'height'  => $height,
                    'path'    => $defaultImage,
                    'orig'    => $defaultImage,
                    'origUrl' => $defaultImage,
                    'url'     => $defaultImage,
                    'rel'     => $defaultImage,
                );

            } else {
                return $this->_jbimage->resize($defaultImage, $width, $height);
            }
        }

        return null;
    }

    /**
     * Is file exists
     *
     * @param string $imagePath
     *
     * @return bool
     */
    protected function _isFileExists($imagePath)
    {
        if (strpos($imagePath, 'http') !== false) {
            return true;

        } else if (JFile::exists($imagePath) || JFile::exists(JPATH_ROOT . '/' . $imagePath)) {
            return true;
        }

        return false;
    }

    /**
     * Trigger on item delete
     */
    public function triggerItemDeleted()
    {
        $removeWithItem = (int)$this->config->get('remove_with_item');
        if (!$removeWithItem) {
            return null;
        }

        $result = array();

        $this->seek(0);
        while ($this->valid()) {
            $result[] = $this->_triggerItemDeleted();
            $this->next();
        }

        return $result;
    }

    /**
     * Each image delete
     */
    protected function _triggerItemDeleted()
    {
        $file = $this->get('file');
        if ($file && JFile::exists(JPATH_ROOT . '/' . $file)) {
            return JFile::delete(JPATH_ROOT . '/' . $file);

        }

        return null;
    }

}