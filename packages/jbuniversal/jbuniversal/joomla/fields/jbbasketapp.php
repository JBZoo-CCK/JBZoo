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
 * Class JFormFieldJBBasketApp
 */
class JFormFieldJBBasketApp extends JFormField
{

    protected $type = 'jbbasketapp';

    public function getInput()
    {
        // get app
        $app = App::getInstance('zoo');

        $applications = $app->table->application->all();

        // create select
        $options = array();
        foreach ($applications as $application) {
            if ((int)$application->getParams()->get('global.jbzoo_cart_config.enable', 0)) {
                $options[] = $app->html->_('select.option', $application->id, $application->name);
            }
        }

        return $app->html->_(
            'select.genericlist',
            $options,
            $this->getName($this->fieldname),
            '',
            'value',
            'text',
            $this->value
        );
    }

}
