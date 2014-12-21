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
 * Class JBTemplateCatalog
 */
class JBTemplateUikit extends JBTemplate
{

    /**
     * On init template
     * @return void
     */
    public function onInit()
    {
        $isAddCss = $this->params->get('global.template.add_css', 0);
        $isAddJs  = $this->params->get('global.template.add_js', 0);

        if ($isAddCss) {
            $this->app->jbassets->css('jbassets:css/uikit.gradient.min.css');
        }

        if ($isAddJs) {
            $this->app->jbassets->css('jbassets:js/uikit.min.js');
        }

        $this->app->jbassets->less('jbassets:less/uikit-hacks.less');
    }

    /**
     * Attributes for jbzoo wrapper
     * @return array|string
     */
    public function wrapperAttrs()
    {
        $attrs        = array();
        $defaultAttrs = parent::wrapperAttrs();

        if ($this->application) {
            if ((int)$this->params->get('global.config.rborder', 1)) {
                $attrs['class'][] = $this->prefix . '-rborder';
            } else {
                $attrs['class'][] = $this->prefix . '-no-border';
            }
        }

        $attrs = array_merge_recursive($defaultAttrs, $attrs);

        return $attrs;
    }

}
