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

jimport('joomla.form.formfield');

// load config
require_once(JPATH_ADMINISTRATOR . '/components/com_zoo/config.php');

/**
 * Class JFormFieldJBRouter
 */
class JFormFieldJBRouter extends JFormField
{

    protected $type = 'jburl';

    public function getInput()
    {
        // get app
        $app = App::getInstance('zoo');

        $router = $app->jbrouter;

        $method = (string)$this->element->attributes()->method;
        $arg1   = (string)$this->element->attributes()->arg1;
        $arg2   = (string)$this->element->attributes()->arg2;
        $arg3   = (string)$this->element->attributes()->arg3;
        $arg4   = (string)$this->element->attributes()->arg4;

        $url = null;
        if (method_exists($router, $method)) {
            $url = $router->$method($arg1, $arg2, $arg3, $arg4);
        }

        return '<pre>' . $url . '</pre>';
    }

}
