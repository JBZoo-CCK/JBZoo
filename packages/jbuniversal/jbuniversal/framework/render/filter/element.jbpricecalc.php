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

        $html = $renderer->render($template, array(
            'price'    => $jbPrice,
            'template' => $template,
            'app_id'   => $this->_params->get('item_application_id')
        ));

        return $html;
    }
}
