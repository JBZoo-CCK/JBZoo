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
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

/**
 * Class JBCartElementPriceparamSelect
 */
class JBCartElementPriceparamSelect extends JBCartElementPriceparam
{

    public function getConfigForm()
    {
        return parent::getConfigForm()->addElementPath(dirname(__FILE__));
    }

    public function editOption($var, $num, $name = null, $value = null)
    {
        return $this->renderLayout($this->app->path->path("elements:option/tmpl/editoption.php"), compact('var', 'num', 'name', 'value'));
    }

}
