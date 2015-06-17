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
 * Class JBTemplateUikit
 */
class JBTemplateUikit extends JBTemplate
{

    /**
     * On init template.
     *
     * @return void
     */
    public function onInit()
    {
        $this->app->jbuikit->assets($this->params);

        $this->app->jbassets->less(array(
            'jbassets:less/uikit.styles.less',
            'jbassets:less/media/desktop.less',
            'jbassets:less/media/tablet.less',
            'jbassets:less/media/mobile.less',
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
        $isAddCss     = $this->params->get('global.template.add_css', 'yes_gradient');
        $isGradient   = ($isAddCss == 'yes_gradient') ? 'yes' : 'no';

        if ($this->application) {
            if (!(int)$this->params->get('global.config.rborder', 1)) {
                $attrs['class'][] = $this->prefix . '-no-border';
            }

            $attrs['class'][] = $this->prefix . '-gradient-' . $isGradient;

            if ($isQuickView = $this->app->jbrequest->get('jbquickview', false)) {
                $attrs['class'][] = 'jbmodal';
            }
        }

        $attrs = array_merge_recursive($defaultAttrs, $attrs);

        return $attrs;
    }

}
