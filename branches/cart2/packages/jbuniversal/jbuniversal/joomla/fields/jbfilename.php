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


jimport('joomla.form.formfield');

// load config
require_once(JPATH_ADMINISTRATOR . '/components/com_zoo/config.php');

/**
 * Class JFormFieldJBFilename
 */
class JFormFieldJBFilename extends JFormField
{

    protected $type = 'jbfilename';

    /**
     * Render control HTML
     * @return mixed
     */
    public function getInput()
    {
        // get app
        $app  = App::getInstance('zoo');
        $ext  = (string)$this->element->attributes()->ext;
        $path = JPath::clean(JPATH_ROOT . $this->element->attributes()->path);

        $options = array();

        if (is_dir($path)) {
            if ($ext) {
                foreach (JFolder::files($path, '^([-_A-Za-z0-9]*)\.' . $ext) as $tmpl) {
                    $tmpl      = basename($tmpl, '.' . $ext);
                    $options[] = $app->html->_('select.option', $tmpl, ucwords($tmpl));
                }
            } else {
                foreach (JFolder::files($path) as $tmpl) {
                    $options[] = $app->html->_('select.option', $tmpl, ucwords($tmpl));
                }
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