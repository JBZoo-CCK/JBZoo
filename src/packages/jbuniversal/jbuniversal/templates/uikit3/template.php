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
