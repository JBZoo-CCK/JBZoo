<?php
/**
 * @package   com_zoo
 * @author    YOOtheme http://www.yootheme.com
 * @copyright Copyright (C) YOOtheme GmbH
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

/**
 * Helper to deal with generic data
 * @package Framework.Helpers
 */
class DataHelper extends AppHelper
{

    /**
     * Class Constructor
     * @param App $app A reference to the global App object
     */
    public function __construct($app)
    {
        parent::__construct($app);

        // load class
        $this->app->loader->register('AppData', 'classes:data.php');
    }

    /**
     * Create a data object
     * @param mixed  $data   The data to load
     * @param string $format The data format (default: json)
     * @return mixed The class representing the data
     * @since 1.0.0
     */
    public function create($data = array(), $format = 'json')
    {
        if ($data instanceof AppData) {
            return $data;
        }

        // load data class
        $class = $format . 'Data';
        if (!class_exists($class)) {
            $this->app->loader->register($class, 'data:' . strtolower($format) . '.php');
        }

        return new $class($data);
    }

}