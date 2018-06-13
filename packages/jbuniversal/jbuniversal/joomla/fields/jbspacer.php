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
 * Class JFormFieldJBSpacer
 */
class JFormFieldJBSpacer extends JFormField
{

    /**
     * @var string
     */
    protected $type = 'jbspacer';


    /**
     *
     */
    public function getInput()
    {
        $value = JText::_($this->element->attributes()->default);
        return '<strong style="width: 100%;float: left;color:#a00;font-size:1.1em"> - = ' . $value . ' = -</strong>';
    }

    /**
     * @return null
     */
    public function getLabel()
    {
        return null;
    }

}