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
 * Class JFormFieldJBPaymentUrl
 */
class JFormFieldJBPaymentUrl extends JFormField
{

    protected $type = 'jbpaymenturl';

    public function getInput()
    {
        // get app
        $app = App::getInstance('zoo');

        $urlType = $this->element->attributes()->urltype;
        $url     = $app->jbrouter->payment($urlType);

        return '<textarea readonly="readonly" class="paymenturl-area">' . $url . '</textarea>';
    }

}
