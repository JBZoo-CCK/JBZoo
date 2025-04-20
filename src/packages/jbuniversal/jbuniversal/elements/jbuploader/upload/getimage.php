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

$jinput = JFactory::getApplication()->input;
$jhttp 	= JHttpFactory::getHttp();

$url = $jinput->get->get('url', '' ,'STRING');
$callback = $jinput->get->get('callback', '', 'STRING');

try {
	
	// Check if the URL is set
	if(!empty($url)) {
		
		// Get the URL and decode to remove any %20, etc
		$url = urldecode($url);
		
		// Get the contents of the URL
		$response = $jhttp->get($url);

		// Check Response
		if ($response->code == '200') {
			$file = $response->body;
		} else {
			header('HTTP/1.0 400 Bad Request');
			echo "No File";
		}
	
		// Check if it is an image
		if(@imagecreatefromstring($file)) {
		
			// Get the image information
			$size = getimagesize($url);
			// Image type
			$type = $size["mime"];
			// Dimensions
			$width = $size[0];
			$height = $size[1];
			// Path Info
			$pathinfo = pathinfo($url);
			$filename = $pathinfo['filename'];
			
			// Setup the data URL
			$type_prefix = "data:" . $type . ";base64,";
			// Encode the image into base64
			$base64file = base64_encode($file);
			// Combine the prefix and the image
			$data_url = $type_prefix . $base64file;
		
			// Setup the return data
			$return_arr = array(
				'width'		=> $width,
				'height'	=> $height,
				'data'		=> $data_url,
				'type' 		=> $type,
				'name' 		=> $filename
			);
													
			// Encode it into JSON
			$return_val = json_encode($return_arr);
		
			// If a callback has been specified
			if(!empty($callback)) {
			
				// Wrap the callback around the JSON
				$return_val = $callback . '(' . $return_val . ');';
			
				// Set the headers to JSON and so they wont cache or expire
				header('Cache-Control: no-cache, must-revalidate');
				header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
				header('Content-type: application/json');
			
				// Print the JSON
				print $return_val;
			
			// No callback was set
			} else {
				header('HTTP/1.0 400 Bad Request');
				print "No callback specified";
			}
		
		// The requested file is not an image
		} else {
			header('HTTP/1.0 400 Bad Request');
			print "Invalid image specified";
		}
	
	// No URL set so error
	} else {
		header('HTTP/1.0 400 Bad Request');
		echo "No URL was specified";
	}
} catch (Exception $e) {	
	header('HTTP/1.0 500 Internal Server Error');
	echo "Internal Server Error";
}
?>