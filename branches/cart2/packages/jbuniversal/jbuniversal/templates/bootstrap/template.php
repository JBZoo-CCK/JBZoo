<?php
/**
 * JBZoo App is universal Joomla CCK, application for YooTheme Zoo component
 *
 * @package     jbzoo
 * @version     2.x Pro
 * @author      JBZoo App http://jbzoo.com
 * @copyright   Copyright (C) JBZoo.com,  All rights reserved.
 * @license     http://jbzoo.com/license-pro.php JBZoo Licence
 * @coder       Segrey Kalistratov <kalistratov.s.m@gmail.com>
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

/**
 * Class JBTemplateBootstrap
 */
class JBTemplateBootstrap extends JBTemplate
{

    /**
     * On init template.
     *
     * @return void
     */
    public function onInit()
    {
        if ($this->params->get('global.template.add_js', true)) {
            $this->app->jbassets->js('jbassets:js/bootstrap.min.js');
        }

        if ($this->params->get('global.template.add_css', true)) {
            $this->app->jbassets->css('jbassets:css/bootstrap.min.css');
        }

        $this->app->jbassets->less(array(
            'jbassets:less/bootstrap.styles.less',
        ));
    }

    /**
     * Attributes for jbzoo wrapper.
     *
     * @return array|string
     */
    public function wrapperAttrs()
    {
        $attrs        = array();
        $defaultAttrs = parent::wrapperAttrs();

        if ($this->application) {
            if (!(int)$this->params->get('global.config.rborder', 1)) {
                $attrs['class'][] = $this->prefix . '-no-border';
            }

        }

        return array_merge_recursive($defaultAttrs, $attrs);
    }

}
