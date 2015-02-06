<?php
/**
 * JBZoo App is universal Joomla CCK, application for YooTheme Zoo component
 * @package     jbzoo
 * @version     2.x Pro
 * @author      JBZoo App http://jbzoo.com
 * @copyright   Copyright (C) JBZoo.com,  All rights reserved.
 * @license     http://jbzoo.com/license-pro.php JBZoo Licence
 * @coder       Andrey Voytsehovsky <kess@jbzoo.com>
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

/**
 * Class JBCartElementOrderUpload
 */
class JBCartElementOrderUpload extends JBCartElementOrder
{

    /**
     * Checks if an element has value
     * @param array $params
     * @return bool
     */
    public function hasValue($params = array())
    {
        $file = $this->app->path->path('root:' . $this->get('file'));

        return !empty($file) && is_readable($file) && is_file($file);
    }

    /**
     * Renders the element
     * @param array $params
     * @return string
     */
    public function render($params = array())
    {
        return $this->edit($params);
    }

    /**
     * For viewing in the admin panel
     * @param array $params
     * @return string
     */
    public function edit($params = array())
    {
        if ($this->get('file')) {
            // TODO add download file with protection
            $relPath = $this->app->path->relative($this->get('file'));

            return '<a href="' . JUri::root() . $relPath . '" target="_blank">' . $relPath . '</a>'
            . ' (' . $this->_getSize() . ')';
        }

        return ' - ';
    }

    /**
     * Binds data
     * @param array $data
     * @return void
     */
    public function bindData($data = array())
    {
        parent::bindData($data);

        // add size to data
        $this->_updateFileSize();
    }

    /**
     * Renders element in an order form
     * @param array $params
     * @return string
     */
    public function renderSubmission($params = array())
    {
        // init vars
        $default = $this->getUserState($params->get('user_field'));
        $upload  = $this->get('file', $default);
        $upload  = is_array($upload) ? '' : $upload; // is uploaded file

        if (!empty($upload)) {
            $upload = basename($upload);
        }

        $maxSize = $this->config->get('max_upload_size', '512') * 1024;
        $maxSize = empty($maxSize) ? null : $maxSize;

        if ($layout = $this->getLayout('submission.php')) {
            return $this->renderLayout($layout, array(
                'upload'          => $upload,
                'maxSizeFormated' => $this->app->filesystem->formatFilesize($maxSize),
                'maxSizeBytes'    => $maxSize,
                'uploadFlag'      => $upload ? 1 : '',
            ));
        }

    }

    /**
     * Validates submission
     * @param $value
     * @param $params
     * @return array
     * @throws AppValidatorException
     */
    public function validateSubmission($value, $params)
    {
        // get old file value
        $old_file = $this->get('file');
        $file     = '';

        try {

            // get the uploaded file information
            foreach ($_FILES as $filesPart) {
                $userfile = $filesPart;
                break;
            }

            // get legal extensions
            $extensions = array_map(create_function('$ext', 'return strtolower(trim($ext));'), explode(',', $this->config->get('upload_extensions', 'png,jpg,doc,mp3,mov,avi,mpg,zip,rar,gz')));

            //get legal mime types
            $mime_types = $this->app->data->create(array_intersect_key($this->app->filesystem->getMimeMapping(), array_flip($extensions)))->flattenRecursive();

            // get max upload size
            $max_size = $this->config->get('max_upload_size', '512') * 1024;
            $max_size = empty($max_size) ? null : $max_size;

            // validate
            $file = $this->app->validator
                ->create('file', compact('mime_types', 'max_size'))
                ->addMessage('mime_types', 'Uploaded file is not of a permitted type.')
                ->clean($userfile);

        } catch (AppValidatorException $e) {

            if ($e->getCode() != UPLOAD_ERR_NO_FILE) {
                throw $e;
            }

            if ($old_file && $value->get('upload')) {
                $file = $old_file;
            }

        }

        if ($params->get('required') && empty($file)) {
            throw new AppValidatorException('Please select a file to upload.');
        }

        if ($userfile['tmp_name'] && is_array($userfile)) {
            // get file name
            $ext       = $this->app->filesystem->getExtension($userfile['name']);
            $base_path = JPATH_ROOT . '/' . $this->_getUploadPath() . '/';
            $file      = $base_path . $userfile['name'];
            $filename  = basename($file, '.' . $ext);

            $i = 1;
            while (JFile::exists($file)) {
                $file = $base_path . $filename . '-' . $i++ . '.' . $ext;
            }

            if (!JFile::upload($userfile['tmp_name'], $file)) {
                throw new AppValidatorException('Unable to upload file.');
            }

            $this->app->zoo->putIndexFile(dirname($file));

            $this->set('file', $this->app->path->relative($file));
            $this->_updateFileSize();
        }

        return compact('file');
    }

    /**
     * Gets upload path from config
     * @return string
     */
    protected function _getUploadPath()
    {
        return trim(trim($this->config->get('upload_directory', 'images/jbzoo/uploads/')), '\/');
    }

    /**
     * Adds file size info
     * @return void
     */
    protected function _updateFileSize()
    {
        if (is_string($this->get('file'))) {
            $filepath = $this->app->path->path('root:' . $this->app->path->relative($this->get('file')));

            if (is_readable($filepath) && is_file($filepath)) {
                $this->set('size', sprintf('%u', filesize($filepath)));
            } else {
                $this->set('size', 0);
            }
        }
    }

    public function loadAssets()
    {
        $this->app->jbassets->js('cart-elements:order/upload/assets/js/upload.js');
    }

    /**
     * @return string
     */
    protected function _getUploadName()
    {
        return 'elements_' . $this->identifier;
    }

    /**
     * Gets file extension
     * @return mixed
     */
    protected function _getExtension()
    {
        return $this->app->filesystem->getExtension($this->get('file'));
    }

    /**
     * Gets the size of a file
     * @return mixed
     */
    protected function _getSize()
    {
        return $this->app->filesystem->formatFilesize($this->get('size', 0));
    }

}
