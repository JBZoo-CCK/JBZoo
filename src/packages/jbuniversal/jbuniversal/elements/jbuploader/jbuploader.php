<?php
/**
 * @package   FL Gallery Image Element for Zoo
 * @author    Дмитрий Васюков http://fictionlabs.ru
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

/**
 * Class ElementJBImage
 */
class ElementJBUploader extends Element
{
    /**
     * @var JBImageHelper
     */
    protected $_jbimagegallery = null;

    /**
     * Class constructor
     */
    public function __construct()
    {
        parent::__construct();

        $this->_jbimagegallery = $this->app->jbimage;
    }

    /**
     * Checks if the repeatables element's value is set.
     * @param array $params render parameter
     * @return bool true, on success
     */
    public function hasValue($params = array())
    {  
        return false;
    }

    /**
     * Get elements search data.
     * @return string Search data
     */
    public function getSearchData()
    {   
        return null;
    }

    public function getGalleryImageControlName($name, $index = 0)
    {
        return "elements[{$this->identifier}][{$index}][{$name}]";
    }

    /**
     * @param array $attrs
     * @return string
     */
    public function _buildAttrs(array $attrs)
    {
        return $this->app->jbhtml->buildAttrs($attrs);
    }

    /**
     * Render
     * @param array $params
     */
    public function render($params = array())
    {   
        return null;
    }

    /**
     * Renders the edit form field.
     * @return string HTML
     */
    public function edit()
    {      
        if (!$this->config->get('upload_scripts', 0)) {
            $this->app->document->addScript('elements:jbuploader/assets/js/load-image.all.min.js');
            $this->app->document->addScript('elements:jbuploader/assets/js/canvas-to-blob.min.js');
            $this->app->document->addScript('elements:jbuploader/assets/js/jquery.iframe-transport.min.js');
            $this->app->document->addScript('elements:jbuploader/assets/js/jquery.fileupload.min.js');
            $this->app->document->addScript('elements:jbuploader/assets/js/jquery.fileupload-process.min.js');
            $this->app->document->addScript('elements:jbuploader/assets/js/jquery.fileupload-image.min.js');
            $this->app->document->addScript('elements:jbuploader/assets/js/jquery.fileupload-validate.min.js');
        }
        
        $this->app->document->addScript('elements:jbuploader/assets/js/jquery.getimagedata.min.js');
        $this->app->document->addScript('elements:jbuploader/assets/js/edit.js');
        $this->app->document->addScript('elements:jbuploader/assets/js/clipboard.min.js');
        $this->app->document->addStylesheet('elements:jbuploader/assets/css/edit.css');

        if ($layout = $this->getLayout('edit.php')) {
            return $this->renderLayout($layout);
        }

        return null;
    }

    /**
     * Get upload image path
     * @return string
     */
    protected function getUploadImagePath()
    {   
        $item = $this->getItem();
        $uploadByUser    = (int)$this->config->get('upload_by_user', 0);
        $uploadByDate    = (int)$this->config->get('upload_by_date', 0);
        $uploadByMonth    = (int)$this->config->get('upload_by_month', 0);
        $uploadDirectory = trim(trim($this->config->get('upload_directory', 'images/zoo/uploads/')), '\/');

        if ($uploadByUser) {
            if ($item->id) {
                $user_id = ($item->created_by) ? $item->created_by : 'quest';
            } else {
                $user = JFactory::getUser();
                $user_id = $user->id;
            }
            $uploadDirectory .= '/'.$user_id;

            return JPath::clean($uploadDirectory);
        } 

        if ($uploadByDate) {
            $uploadDirectory .= '/'.date("d-m-Y");
            return JPath::clean($uploadDirectory);
        }

        if ($uploadByMonth) {
            $uploadDirectory .= '/'.date("mY");
            return JPath::clean($uploadDirectory);
        }

        return JPath::clean($uploadDirectory);
    }

    /**
     * Get uploaded file path 
     * @return string
     */

    protected function getUploadedFilePath($userfile)
    {
        $basePath = JPATH_ROOT . '/' . $this->getUploadImagePath() . '/';
        $file     = $basePath . $userfile;

        return JPath::clean($this->app->path->relative($file));
    }

    /**
     * Is file exists
     *
     * @param string $imagePath
     *
     * @return bool
     */
    protected function isFileExists($imagePath)
    {
        if (strpos($imagePath, 'http') !== false) {
            return true;

        } else if (JFile::exists($imagePath) || JFile::exists(JPATH_ROOT . '/' . $imagePath)) {
            return true;
        }

        return false;
    }
}