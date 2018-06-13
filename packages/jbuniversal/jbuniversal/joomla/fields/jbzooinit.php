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
require_once(JPATH_ROOT . '/media/zoo/applications/jbuniversal/framework/jbzoo.php');

/**
 * Class JFormFieldJBZooInit
 */
class JFormFieldJBZooInit extends JFormField
{

    protected $type = 'jbzooinit';

    /**
     * Get input
     * @return null|string
     */
    public function getInput()
    {
        JBZoo::init();

        $zoo = App::getInstance('zoo');
        $zoo->system->language->load('com_jbzoo', $zoo->path->path('applications:jbuniversal'), null, true);
        $zoo->system->language->load('com_jbzoostd', $zoo->path->path('applications:jbuniversal'), null, true);
        $zoo->jbassets->admin();

        return null;
    }

    /**
     * @return null|string
     */
    public function getLabel()
    {
        return null;
    }

}