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
 * Class JFormFieldJBTemplates
 */
class JFormFieldJBTemplates extends JFormField
{

    protected $type = 'jbtemplates';

    /**
     * @return null|string
     */
    public function getInput()
    {
        $app         = App::getInstance('zoo');
        $application = $app->zoo->getApplication();
        $templates   = $application->getTemplates();

        $options = array();

        foreach ($templates as $key => $template) {
            $options[$key] = $template->getMetaData()->name;
        }

        if (!$this->value) {
            $this->value = 'catalog';
        }

        return $app->jbhtml->select($options, $this->getName($this->fieldname), array(), $this->value);
    }
}