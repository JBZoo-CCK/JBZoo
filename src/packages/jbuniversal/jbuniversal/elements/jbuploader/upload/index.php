<?php

/**
 * @package   FL Gallery Image Element for Zoo
 * @author    Дмитрий Васюков http://fictionlabs.ru
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

define('_JEXEC', 1);
define( 'JPATH_BASE', realpath(dirname(__FILE__).'/../../../../../../..'));
!defined('JBZOO_APP_GROUP') && define('JBZOO_APP_GROUP', 'jbuniversal');
!defined('DIRECTORY_SEPERATOR') && define('DIRECTORY_SEPERATOR', '/');
!defined('DS') && define('DS', DIRECTORY_SEPARATOR);

error_reporting(E_ALL | E_STRICT);
 
/* Required files */
require_once JPATH_BASE . '/includes/defines.php';
require_once JPATH_BASE . '/includes/framework.php';

JFactory::getApplication('site')->initialise();

require_once JPATH_BASE . '/administrator/components/com_zoo/config.php';
require_once JPATH_BASE . '/media/zoo/applications/jbuniversal/framework/jbzoo.php';
require('UploadHandler.php');

JBZoo::init();

$zoo = App::getInstance('zoo');

if (isset($_REQUEST['elementId'])) {
	//$elementId = htmlentities(strip_tags(JString::trim($_REQUEST['elementId'])), ENT_QUOTES, "UTF-8");
    $elementId = htmlentities(strip_tags(JString::trim($_REQUEST['elementId'])));
	$element = $zoo->jbentity->getItemTypesData();
	$params = $element[$elementId];
	$uploadDirectory = trim(trim($params['upload_directory']), '\/');
	$watermark_path = trim(trim($params['watermark_path']), '\/');
	$site_url = str_replace('media/zoo/applications/jbuniversal/elements/jbuploader/upload/', '', JURI::root());

	$upload_dir = $site_url.$uploadDirectory;

	if ($params['upload_by_user'] || $params['upload_by_date'] || $params['upload_by_month']) {
		$upload_dir .= '/';
	}

	$options = array(
		'upload_dir'		=>	JPATH_BASE.'/'.$uploadDirectory.'/', 
		'upload_url'		=> 	$upload_dir,
		'user_dirs' 		=> 	true,
		'param_name'		=> 	$elementId.'-files',
		'watermark_enable'  =>  $params['watermark_enable'],
		'watermark_path'  	=>  JPATH_BASE.'/'.$watermark_path,
		'upload_by_user'  	=>  $params['upload_by_user'],
		'upload_by_date'  	=>  $params['upload_by_date'],
		'upload_by_month'  	=>  $params['upload_by_month']
	);

	class CustomUploadHandler extends UploadHandler {
	    protected function get_user_id() {
	    	return $this->handle_form_data();
	    }
	}

	$upload_handler = new CustomUploadHandler($options);
} else {
	echo 'Error, Sorry =(';
}