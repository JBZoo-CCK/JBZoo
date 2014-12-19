<?php
/**
 * JBZoo App is universal Joomla CCK, application for YooTheme Zoo component
 *
 * @package     jbzoo
 * @version     2.x Pro
 * @author      JBZoo App http://jbzoo.com
 * @copyright   Copyright (C) JBZoo.com,  All rights reserved.
 * @license     http://jbzoo.com/license-pro.php JBZoo Licence
 * @coder       Andrey Voytsehovsky <kess@jbzoo.com>
 */


class JBCartElementOrderUpload extends JBCartElementOrder
{


    /**
     * Gets the size of a file
     * @return mixed
     */
    public function getSize()
    {
        return $this->app->filesystem->formatFilesize($this->get('size', 0));
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

            $html[] = '<a href="/';
            $html[] = $this->app->path->relative($this->get('file'));
            $html[] = '">';
            $html[] = $this->app->path->relative($this->get('file'));
            $html[] = '</a>';
            $html[] = ' <span>(';
            $html[] = $this->getSize();
            $html[] = ')</span>';

            return implode("", $html);
        } else {

            return ' - ';
        }
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
        $upload = $this->get('file');

        // is uploaded file
        $upload = is_array($upload) ? '' : $upload;

        if (!empty($upload)) {
            $upload = basename($upload);
        }

        $max_size = $this->config->get('max_upload_size', '512') * 1024;
        $max_size = empty($max_size) ? null : $max_size;

        if ($layout = $this->getLayout('submission.php')) {
            return $this->renderLayout($layout,
                compact('upload', 'max_size')
            );
        }

    }


    /**
     * Validates submission
     * @param $value
     * @param $params
     * @return array
     * @throws AppException
     * @throws AppValidatorException
     * @throws Exception
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

        if ($userfile && is_array($userfile)) {
            // get file name
            $ext = $this->app->filesystem->getExtension($userfile['name']);
            $base_path = JPATH_ROOT . '/' . $this->_getUploadPath() . '/';
            $file = $base_path . $userfile['name'];
            $filename = basename($file, '.' . $ext);

            $i = 1;
            while (JFile::exists($file)) {
                $file = $base_path . $filename . '-' . $i++ . '.' . $ext;
            }

            if (!JFile::upload($userfile['tmp_name'], $file)) {
                throw new AppException('Unable to upload file.');
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


    /**
     * Checks if an element has value
     * @param array $params
     * @return bool
     */
    public function hasValue($params = array())
    {
        // init vars
        $file = $this->app->path->path('root:' . $this->get('file'));
        return !empty($file) && is_readable($file) && is_file($file);
    }



    /**
     * Gets file extension
     * @return mixed
     */
    public function getExtension()
    {
        return $this->app->filesystem->getExtension($this->get('file'));
    }


}
