<?php
/**
 * JBZoo App is universal Joomla CCK, application for YooTheme Zoo component
 *
 * @package     jbzoo
 * @version     2.x Pro
 * @author      JBZoo App http://jbzoo.com
 * @copyright   Copyright (C) JBZoo.com,  All rights reserved.
 * @license     http://jbzoo.com/license-pro.php JBZoo Licence
 * @coder       Alenxader Oganov <t_tapak@yahoo.com>
 */

/**
 * Class JBPriceParamsHelper
 */
class JBPriceParamsHelper extends AppHelper
{
    public function getJBPriceElements()
    {
        $elements = array();
        $application = $this->app->zoo->getApplication();
        foreach ($application->getTypes() as $type) {

            $typeElements = $type->getElementsByType('jbpriceadvance');
            if (!empty($typeElements)) {

                foreach ($typeElements as $key => $element) {
                    $elements[$key] = ucfirst($type->identifier) . ' - ' . ucfirst($element->config->get('name'));
                }
            }
        }

        return $elements;
    }
}