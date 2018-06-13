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
    public function create($data = [], $format = 'json')
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