<?php
/**
 * JBZoo App is universal Joomla CCK, application for YooTheme Zoo component
 * @package     jbzoo
 * @version     2.x Pro
 * @author      JBZoo App http://jbzoo.com
 * @copyright   Copyright (C) JBZoo.com,  All rights reserved.
 * @license     http://jbzoo.com/license-pro.php JBZoo Licence
 * @coder       Denis Smetannikov <denis@jbzoo.com>
 */

// no direct access
defined('_JEXEC') or die('Restricted access');


/**
 * Class JBCartElementNotificationSmsuslugiru
 */
class JBCartElementNotificationSmsuslugiru extends JBCartElementNotification
{
    const URL_HTTP  = 'http://lcab.sms-uslugi.ru/';
    const URL_HTTPS = 'https://lcab.sms-uslugi.ru/';

    /**
     * Launch notification
     * @return void
     */
    public function notify()
    {
        $phones   = $this->_getPhones();
        $login    = JString::trim($this->config->get('sms_login'));
        $password = JString::trim($this->config->get('sms_password'));
        $text     = $this->_macros->renderText($this->config->get('message'), $this->getOrder());

        if (
            !empty($login) && !empty($password) &&
            !empty($phones) && !empty($text)
        ) {

            $this->_sendSMS($phones, array(
                'login'    => $login,
                'password' => $password,
                'text'     => $text,
            ));
        }
    }

    /**
     * Send sms
     * @param array $phones
     * @param array $params
     * @return array|bool
     */
    protected function _sendSMS($phones, $params)
    {
        $params['action'] = 'send';

        $dataXml = '';
        foreach ($params as $key => $value) {
            $dataXml .= "<$key>$value</$key>" . PHP_EOL;
        }

        foreach ($phones as $phone) {
            $dataXml .= '<to number="' . $phone . '"></to>';
        }

        $xml = implode(PHP_EOL, array(
            '<?xml version=\'1.0\' encoding=\'UTF-8\'?>',
            '<data>',
            $dataXml,
            '</data>'
        ));

        $response = $this->app->jbhttp->request($this->_getURI(), $xml, array('method' => 'POST'));
        $response = json_decode($response, true);

        return $response;
    }

    /**
     * @return array
     */
    protected function _getPhones()
    {
        $result = array();

        // custom phones
        $phones = $this->app->jbstring->parseLines($this->config->get('phones', ''));

        // form field
        $elements = $this->config->get('userphone', array());
        if (!empty($elements)) {
            foreach ($elements as $elementID) {

                if ($element = $this->getOrder()->getFieldElement($elementID)) {
                    $data     = $element->data();
                    $phones[] = $data->get('value');
                }
            }
        }

        // clean & check
        foreach ($phones as $phone) {
            $result[] = $this->app->jbvars->phone($phone);
        }
        $result = array_filter($result);
        $result = array_unique($result);

        return $result;
    }

    /**
     * Make URI for request
     * @return string
     */
    private function _getURI()
    {
        if ((int)$this->config->get('https', 0)) {
            $address = self::URL_HTTPS . "API/XML/send.php?returnType=json";
        } else {
            $address = self::URL_HTTP . "API/XML/send.php?returnType=json";
        }

        return $address;
    }

}
