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

/**
 * Class JBCartElementNotificationSMSServices
 */
class JBCartElementNotificationSMSServices extends JBCartElementNotification
{
    /**
     * Uri to make request
     *
     * @var string
     */
    public $httpURI = 'http://lcab.sms-uslugi.ru/';

    /**
     * Uri to make request
     *
     * @var string
     */
    public $httpsURI = 'https://lcab.sms-uslugi.ru/';

    /**
     * String encoding
     *
     * @var string
     */
    public $charset;

    const HTTPS_CHARSET = 'utf-8';

    /**
     * Launch notification
     * @return void
     */
    public function notify()
    {
        $params = $this->_getParams();

        $this->sms($params->get('params'), $params->get('phones'));
    }

    /**
     * Make URI for request
     *
     * @param $action
     *
     * @return string
     */
    public function getURI($action)
    {
        $https   = (int)$this->config->get('https', 1);
        $address = $this->httpURI . "API/XML/" . $action . ".php";

        if ($https) {
            $address = $this->httpsURI . "API/XML/" . $action . ".php";
        }

        $address .= "?returnType=json";

        return $address;
    }

    /**
     * Send sms
     *
     * @param string $action
     * @param array  $params
     * @param array  $phones
     *
     * @return array|bool
     */
    public function sms($params = array(), $phones = array(), $action = 'send')
    {
        $someXML = '';

        if (!empty($phones)) {

            foreach ($phones as $phone) {

                $someXML .= '<to number="' . $phone . '">';
                $someXML .= '</to>';
            }
        }
        $xml = $this->makeXML($params, $someXML);
        $xml = $this->replace($xml);

        $response = $this->_callService($this->getURI($action), parent::HTTP_POST, $xml);

        return $response;
    }

    /**
     * Make xml data for request
     *
     * @param        $params
     * @param string $someXML
     *
     * @return string
     */
    public function makeXML($params, $someXML = "")
    {
        $xml = "<?xml version='1.0' encoding='UTF-8'?>
        <data>";
        foreach ($params as $key => $value) {
            $xml .= "<$key>$value</$key>";
        }
        $xml .= "$someXML
		</data>";

        return $xml;
    }

    /**
     * Decoding the result of API call
     *
     * @param $responseBody
     *
     * @return mixed
     */
    public function processingData($responseBody)
    {
        return json_decode($responseBody, true);
    }

    /**
     * Get prepared params from config
     *
     * @return array
     */
    protected function _getParams()
    {
        $phones   = $this->config->get('userphone', array());
        $admPhone = $this->config->get('phones', array());

        if (!empty($admPhone)) {

            $admPhones = explode("\n", $admPhone);
            foreach ($admPhones as $phone) {
                $phones[] = $phone;
            }
        }

        $params = array(
            'params' =>
                $this->app->data->create(array(
                    'login'    => $this->config->get('login'),
                    'password' => $this->config->get('password'),
                    'text'     => $this->config->get('message')
                )),
            'phones' => $this->app->data->create($phones)
        );

        return $this->app->data->create($params);
    }
}
