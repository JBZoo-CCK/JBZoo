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
 * Class ElementsJBUniversalController
 */
class ElementsJBUniversalController extends JBUniversalController
{

    /**
     * Upload
     * @throws AppException
     */
    function upload()
    {

        $upload    = $this->_jbrequest->get('upload');
        $param     = $this->_jbrequest->get('paramName');
        $accept    = $this->_jbrequest->get('accept');
        $watermark = $this->_jbrequest->get('watermark');
        $position  = $this->_jbrequest->get('position');

        if (empty($upload) || empty($param))
        {
            $this->zoo->jbajax->send();
        }

        if ($accept)
        {
            $accept = '/\.(' . $accept . ')$/i';
        }

        $upload_dir = JPATH_ROOT . '/' . $upload . '/';
        $upload_url = JURI::base() . $upload . '/';

        $options = array(
            'upload_dir'        => $upload_dir,
            'upload_url'        => $upload_url,
            'script_url'        => $this->zoo->jbrouter->elementsUpload(),
            'param_name'        => $param,
            'user_dirs'         => false,
            'image_versions'    => array(),
            'accept_file_types' => $accept,
        );

        if ($watermark)
        {
            $options['watermark_enable']   = 1;
            $options['watermark_path']     = JPATH_BASE . '/' . $watermark;
            $options['watermark_position'] = $position;
        }

        return new JBUpload($options);
    }

    /**
     * Get form token
     * @throws AppException
     */
    function token()
    {
        // $result = JSession::getFormToken(false);  //todofixj4
        $result = Joomla\CMS\Session\Session::getFormToken(false);
        $this->zoo->jbajax->send(array(), $result);
    }
}