<?php
/**
 * JBZoo App is universal Joomla CCK, application for YooTheme Zoo component
 *
 * @package     jbzoo
 * @version     2.x Pro
 * @author      JBZoo App http://jbzoo.com
 * @copyright   Copyright (C) JBZoo.com,  All rights reserved.
 * @license     http://jbzoo.com/license-pro.php JBZoo Licence
 * @coder       Alexander Oganov <t_tapak@yahoo.com>
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

/**
 * Class JBFilterElementJBPriceCalc
 */
class JBFilterElementJBPriceCalc extends JBFilterElement
{

    /**
     * Render HTML code for element
     * @return string|null
     */
    public function html()
    {
        $template = $this->_params->get('jbprice_filter_template', 'default');
        $renderer = $this->app->jbrenderer->create('jbpricefilter');
        $jbPrice  = $this->app->jbfilter->getElement($this->_identifier);
        $renderer->setModuleParams($this->_params->moduleParams);
        $jbPrice->setTemplate('_filter_layout', $template);

        $html = $renderer->render($template, array(
            'price'    => $this->app->jbfilter->getElement($this->_identifier),
            'template' => $template,
            'app_id'   => $this->_params->get('item_application_id')
        ));

        return $html;
    }
}
