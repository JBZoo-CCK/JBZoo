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
 * Class JBCartElementShippingSdek
 */
class JBCartElementShippingSdek extends JBCartElementShipping
{
    const CACHE_TTL = 1440;
    const URL_CITY  = 'https://api.cdek.ru/city/getListByTerm/jsonp.php';

    protected $_currency    = 'rub';
    protected $_ajaxData    = array();

    /**
     * Class constructor
     * @param App    $app
     * @param string $type
     * @param string $group
     */
    public function __construct($app, $type, $group)
    {
        parent::__construct($app, $type, $group);

        $this->registerCallback('sdekService');
        $this->registerCallback('sdekTemplate');

        JFactory::getLanguage()->load('com_jbzoo_cart_elements_shipping_sdek', $this->app->path->path('jbapp:cart-elements').'/shipping/sdek', null, true);

        $this->app->jbassets->addVar('JBZOO_ELEMENT_SHIPPING_SDEK_CHANGE', JText::_('JBZOO_ELEMENT_SHIPPING_SDEK_CHANGE'));
        $this->app->jbassets->addVar('JBZOO_ELEMENT_SHIPPING_SDEK_SELECT', JText::_('JBZOO_ELEMENT_SHIPPING_SDEK_SELECT'));
    }

    /**
     * @return array
     */
    public function getAjaxData()
    {   
        return array(
            'goods'     => $this->app->jbassets->toJSON($this->_getWeight()),
        );
    }

    /**
     * @return $this
     */
    public function loadAssets()
    {   
        $this->app->jbassets->js('cart-elements:shipping/sdek/assets/js/widget.js');

        parent::loadAssets();
    }

    /**
     * @return JBCartValue
     */
    public function getRate()
    {   
        if ($this->isFree()) {
            $freePrice = ($this->config->get('free_courier', 0) && !empty($this->get('tariff', '')) && $this->get('tariff', '') != 136) ? $this->config->get('free_courier', 0) : 0;

            return $this->_order->val($freePrice);
        }

        $price  = $this->get('value');
        $summ   = $this->_order->val(0, $this->_currency);

        if ($price) {
            $summ->set($price, $this->_currency);
        }

        return $summ;
    }

    /**
     * @return string
     */
    protected function _getDefaultCity()
    {
        $from = $this->config->get('from');
        return $from['city-id'];
    }

    /**
     * @return string
     */
    protected function _getDefaultCityName()
    {   
        $from = $this->config->get('from');
        return $from['city-name'];
    }

    /**
     * @param array $params
     * @return mixed|string
     */
    public function renderSubmission($params = array())
    {   
        if ($layout = $this->getLayout('submission.php')) {
            return self::renderLayout($layout, array(
                'params'    => $params,
                'address'   => $this->get('address'),
                'pvz'       => $this->get('pvz'),
                'tariff'    => $this->get('tariff'),
                'to'        => $this->get('to'),
                'value'     => $this->get('value'),
                'rate'      => $this->config->get('rate') ? JString::trim($this->config->get('rate')) : 1,
                'goods'     => $this->app->jbassets->toJSON($this->_getWeight()),
            ));
        }
    }

    /**
     * Validates the submitted element
     * @param $value
     * @param $params
     * @return array
     * @throws JBCartElementShippingException
     */
    public function validateSubmission($value, $params)
    {   
        $value      = $this->app->data->create($value);
        $city       = $value->get('to')['city-name'];
        $address    = $value->get('address');
        $price      = $value->get('value');
        $tariff     = $value->get('tariff');
        
        if (empty($city) || empty($address) || empty($price) || empty($tariff)) {
            throw new JBCartElementShippingException(JText::_('JBZOO_ELEMENT_SHIPPING_SDEK_EXCEPTION'));
        }

        // for calculate rate
        $this->bindData($value);

        $rate = $this->getRate();
        $value->set('rate', $rate->data(true));

        return $value;
    }

    /**
     * @return array
     */
    protected function _getWeight()
    {
        $items      = $this->_order->getItems();
        $goods      = array();

        foreach ($items as $item) {
            $properties = $this->getProperties($item);
            $quantity   = $item->get('quantity');

            for ($i = 1; $i <= $quantity; $i++) { 
                $goods[] = $properties;
            }
        }

        return $goods;
    }

    /**
     * @param string           $name
     * @param string|array     $value
     * @param string           $controlName
     * @param SimpleXMLElement $node
     * @param SimpleXMLElement $parent
     * @return mixed
     */
    public static function getCity($name, $value, $controlName, $node, $parent)
    {   
        App::getInstance('zoo')->jbassets->js('cart-elements:shipping/sdek/assets/js/sdekedit.js');

        $url = self::URL_CITY.'/city/getListByTerm/jsonp.php';

        $html = array();

        $html[] = '<div class="jsGetCity-'.$name.'">';
        $html[] = App::getInstance('zoo')->html->_('control.text', $controlName.'['.$name.'][city-name]', isset($value['city-name']) ? $value['city-name'] : '', 'class="city-name jsCityName"');
        $html[] = App::getInstance('zoo')->html->_('control.text', $controlName.'['.$name.'][city-id]', isset($value['city-id']) ? $value['city-id'] : '', 'class="city-id jsCityId hidden"');
        $html[] = '</div>';

        $html[] = App::getInstance('zoo')->jbassets->widget('.jsGetCity-'.$name, 'JBZooSdekedit', array(
            'url'   => $url,
        ), true);

        return implode("\n", $html);
    }

    /**
     * @return AppParameterForm
     */
    public function getConfigForm()
    {
        return parent::getConfigForm()->addElementPath(dirname(__FILE__) . '/fields');
    }

    /**
     * @param json $jsonItem
     * @return array
     */

    public function getProperties($jsonItem) 
    {   
        $source         = $this->config->get('source', 'price');
        $defaultWeight  = $this->config->get('default_weight', '0.2');
        $defaultLength  = $this->config->get('default_length', '0.1');
        $defaultWidth   = $this->config->get('default_width', '0.1');
        $defaultHeight  = $this->config->get('default_height', '0.1');

        $weight = $length = $width = $height  = 0;

        if ($source == 'price') {
            $variations = $jsonItem->get('variations');

            if (isset($variations[0])) {
                $properties = isset($variations[0]['_properties']) ? $variations[0]['_properties'] : 0;

                if ($properties) {
                    $length = $this->clear($properties['length']);
                    $width  = $this->clear($properties['width']);
                    $height = $this->clear($properties['height']);
                }
                
                $weight = isset($variations[0]['_weight']) ? $this->clear($variations[0]['_weight']['value']) : 0;
            }
        } else {
            $item = $jsonItem->get('item');

            $elementWeight  = $this->config->get('element_weight');
            $elementLength  = $this->config->get('element_length');
            $elementWidth   = $this->config->get('element_width');
            $elementHeight  = $this->config->get('element_height');

            $elementWeight  = $item->getElement($elementWeight);
            $elementLength  = $item->getElement($elementLength);
            $elementWidth   = $item->getElement($elementWidth);
            $elementHeight  = $item->getElement($elementHeight);

            if ($elementWeight) {
                $weight = $this->clear($elementWeight->render());
            }

            if ($elementLength) {
                $length = $this->clear($elementLength->render());
            }

            if ($elementWidth) {
                $width = $this->clear($elementWidth->render());
            }

            if ($elementHeight) {
                $height = $this->clear($elementHeight->render());
            }
        }

        $weight = $weight ? $weight : $defaultWeight;
        $length = $length ? $length : $defaultLength;
        $width  = $width ? $width : $defaultWidth;
        $height = $height ? $height : $defaultHeight;

        return array(
            'weight' => $weight,
            'length' => $length * 100,
            'width'  => $width * 100,
            'height' => $height * 100,
        );
    }

    /**
     * @param string    $var
     * @return string
     */

    public function clear($var)
    {
        //$clearVar = htmlentities(strip_tags(JString::trim($var)), ENT_QUOTES, "UTF-8");
        $clearVar = strip_tags(JString::trim($var));
        $clearVar = str_replace(array('см.', 'м.', 'мл.', 'л.', 'кг.', 'г.', ','), array('', '', '', '', '', '', '.'), $clearVar);

        return $clearVar;
    }

    /**
     * @return
     */

    public function sdekService()
    {   
        $account        = $this->config->get('login');
        $key            = $this->config->get('password');
        $sdekService    = new ISDEKservice($account, $key);

        $sdekService::setTarifPriority(
            array(137),
            array(136)
        );

        $action = $this->app->jbrequest->get('isdek_action');

        if (method_exists('ISDEKservice', $action)) {
            $sdekService::$action($_REQUEST);
        }
    }

    /**
     * @return string
    */
    public function sdekTemplate()
    {   
        $tplPath    = $this->app->path->path('cart-elements:shipping/sdek/tmpl/widget/');
        $files      = JFolder::files($tplPath);

        $arTPL = array();

        foreach ($files as $filesname) {
            $file_tmp = explode('.', $filesname);
            $arTPL[strtolower($file_tmp[0])] = file_get_contents($tplPath . '/' . $filesname);
        }

        return preg_replace('/<!--(.|\s)*?-->/', '', str_replace(array('\r','\n','\t',"\n","\r","\t"), '', json_encode($arTPL)));
    }

    /**
     * @return string
     */
    protected function _getAjaxSdekTemplateUrl()
    {
        return $this->getAjaxUrl('sdekTemplate');
    }

    /**
     * @return string
     */
    protected function _getAjaxSdekServiceUrl()
    {
        return $this->getAjaxUrl('sdekService');
    }
}


// SDEK Service Class


class ISDEKservice
{
    protected static $account       = '';
    protected static $key           = '';
    protected static $tarifPriority = false;

    /**
     * Class constructor
     * @param App    $app
     * @param string $type
     * @param string $group
     */
    public function __construct($account, $key)
    {
        self::$account  = $account;
        self::$key      = $key;
    }

    // Workout
    public static function setTarifPriority($arCourier, $arPickup)
    {
        self::$tarifPriority = array(
            'courier' => $arCourier,
            'pickup'  => $arPickup
        );
    }

    public static function getPVZ()
    {
        $arPVZ = self::getPVZFile();
        if ($arPVZ) {
            self::toAnswer(array('pvz' => $arPVZ));
        }
        self::printAnswer();
    }

    public static function getLang()
    {
        self::toAnswer(array('LANG' => self::getLangArray()));
        self::printAnswer();
    }

    public static function calc($data)
    {
        if (!isset($data['shipment']['tarifList'])) {
            $data['shipment']['tariffList'] = self::$tarifPriority[$data['shipment']['type']];
        }
        if (isset($_SERVER['HTTP_REFERER']) && !empty($_SERVER['HTTP_REFERER'])) $data['shipment']['ref'] = $_SERVER['HTTP_REFERER'];

        if (!$data['shipment']['cityToId']) {
            $cityTo = self::sendToCity($data['shipment']['cityTo']);
            if ($cityTo && $cityTo['code'] === 200) {
                $pretendents = json_decode($cityTo['result']);
                if ($pretendents && isset($pretendents->geonames)) {
                    $data['shipment']['cityToId'] = $pretendents->geonames[0]->id;
                }
            }
        }

        if ($data['shipment']['cityToId']) {
            $answer = self::calculate($data['shipment']);

            if ($answer) {
                $answer['type'] = $data['shipment']['type'];
                if ($data['shipment']['timestamp']) {
                    $answer['timestamp'] = $data['shipment']['timestamp'];
                }
                self::toAnswer($answer);
            }
        } else {
            self::toAnswer(array('error' => 'City to not found'));
        }

        self::printAnswer();
    }

    public static function getCity($data)
    {
        if ($data['city']) {
            $result = self::sendToCity($data['city']);
            if ($result && $result['code'] == 200) {
                $result = json_decode($result['result']);
                if (!isset($result->geonames)) {
                    self::toAnswer(array('error' => 'No cities found'));
                } else {
                    self::toAnswer(array(
                        'id'      => $result->geonames[0]->id,
                        'city'    => $result->geonames[0]->cityName,
                        'region'  => $result->geonames[0]->regionName,
                        'country' => $result->geonames[0]->countryName
                    ));
                }
            } else {
                self::toAnswer(array('error' => 'Wrong answer code from server : ' . $result['code']));
            }
        } else {
            self::toAnswer(array('error' => 'No city to search given'));
        }

        self::printAnswer();
    }

    // PVZ
    protected static function getPVZFile()
    {

        $arPVZ = self::requestPVZ();

        return $arPVZ;
    }

    protected static function requestPVZ()
    {
        if (!function_exists('simplexml_load_string')) {
            self::toAnswer(array('error' => 'No php simplexml-library installed on server'));
            return false;
        }

        $request = self::sendToSDEK('pvzlist', false, 'type=ALL' .(isset($_REQUEST['lang'])? '&lang='.$_REQUEST['lang'] : '') );
        $arLL = array();
        if ($request && $request['code'] == 200) {
            $xml = simplexml_load_string($request['result']);

            $arList = array('PVZ' => array(), 'CITY' => array(), 'REGIONS' => array(), 'CITYFULL' => array(), 'COUNTRIES' => array());

            foreach ($xml as $key => $val) {

                if ($_REQUEST['country'] && $_REQUEST['country'] != 'all' && ((string)$val['CountryName'] != $_REQUEST['country'])) {
                    continue;
                }

                $cityCode = (string)$val['CityCode'];
                $type = 'PVZ';
                $city = (string)$val["City"];
                if (strpos($city, '(') !== false)
                    $city = trim(substr($city, 0, strpos($city, '(')));
                if (strpos($city, ',') !== false)
                    $city = trim(substr($city, 0, strpos($city, ',')));
                $code = (string)$val["Code"];

                $arList[$type][$cityCode][$code] = array(
                    'Name'           => (string)$val['Name'],
                    'WorkTime'       => (string)$val['WorkTime'],
                    'Address'        => (string)$val['Address'],
                    'Phone'          => (string)$val['Phone'],
                    'Note'           => (string)$val['Note'],
                    'cX'             => (string)$val['coordX'],
                    'cY'             => (string)$val['coordY'],
                    'Dressing'       => ($val['IsDressingRoom'] == 'true'),
                    'Cash'           => ($val['HaveCashless'] == 'true'),
                    'Station'        => (string)$val['NearestStation'],
                    'Site'           => (string)$val['Site'],
                    'Metro'          => (string)$val['MetroStation'],
                    'AddressComment' => (string)$val['AddressComment'],
                    'CityCode'       => (string)$val['CityCode'],
                );
                if ($val->WeightLimit) {
                    $arList[$type][$cityCode][$code]['WeightLim'] = array(
                        'MIN' => (float)$val->WeightLimit['WeightMin'],
                        'MAX' => (float)$val->WeightLimit['WeightMax']
                    );
                }

                $arImgs = array();
                if (!is_array($val->OfficeImage)) {
                    $arToCheck = array(array('url' => (string)$val->OfficeImage['url']));
                } else {
                    $arToCheck = $val->OfficeImage;
                }

                foreach ($val->OfficeImage as $img) {
                    if (strstr($_tmpUrl = (string)$img['url'], 'http') === false) {
                        continue;
                    }
                    $arImgs[] = (string)$img['url'];
                }

                if (count($arImgs = array_filter($arImgs)))
                    $arList[$type][$cityCode][$code]['Picture'] = $arImgs;
                if ($val->OfficeHowGo)
                    $arList[$type][$cityCode][$code]['Path'] = (string)$val->OfficeHowGo['url'];

                if (!array_key_exists($cityCode, $arList['CITY'])) {
                    $arList['CITY'][$cityCode] = $city;
                    $arList['CITYREG'][$cityCode] = (int)$val['RegionCode'];
                    $arList['REGIONSMAP'][(int)$val['RegionCode']][] = (int)$cityCode;
                    $arList['CITYFULL'][$cityCode] = (string)$val['CountryName'] . ' ' . (string)$val['RegionName'] . ' ' . $city;
                    $arList['REGIONS'][$cityCode] = implode(', ', array_filter(array((string)$val['RegionName'], (string)$val['CountryName'])));
                }

            }

            krsort($arList['PVZ']);

            return $arList;
        } elseif ($request) {
            self::toAnswer(array('error' => 'Wrong answer code from server : ' . $request['code']));
            return false;
        }
    }

    // Calculation
    protected static function calculate($shipment)
    {
        $headers = self::getHeaders();

        $arData = array(
            'dateExecute'    => $headers['date'],
            'version'        => '1.0',
            'authLogin'      => $headers['account'],
            'secure'         => $headers['secure'],
            'senderCityId'   => $shipment['cityFromId'],
            'receiverCityId' => $shipment['cityToId'],
            'ref'            => $shipment['ref'],
            'widget'         => 1,
            'tariffId'       => isset($shipment['tariffId']) ? $shipment['tariffId'] : false
        );

        if ($shipment['tariffList']) {
            foreach ($shipment['tariffList'] as $priority => $tarif) {
                $tarif = intval($tarif);
                $arData['tariffList'] [] = array(
                    'priority' => $priority + 1,
                    'id'       => $tarif
                );
            }
        }

        if ($shipment['goods']) {
            $arData['goods'] = array();
            foreach ($shipment['goods'] as $arGood) {
                $arData['goods'] [] = array(
                    'weight' => $arGood['weight'],
                    'length' => $arGood['length'],
                    'width'  => $arGood['width'],
                    'height' => $arGood['height']
                );
            }
        }

        $result = self::sendToCalculate($arData);

        if ($result && $result['code'] == 200) {
            if (!is_null(json_decode($result['result']))) {
                return json_decode($result['result'], true);
            } else {
                self::toAnswer(array('error' => 'Wrong server answer'));
                return false;
            }
        } else {
            self::toAnswer(array('error' => 'Wrong answer code from server : ' . $result['code']));
            return false;
        }
    }

    // API
    protected static function sendToSDEK($where, $XML = false, $get = false)
    {
        $where .= '.php' . (($get) ? "?" . $get : '');
        $where = 'https://integration.cdek.ru/' . $where;

        if ($XML)
            $XML = array('xml_request' => $XML);

        return self::client($where, $XML);
    }

    protected static function getHeaders()
    {
        $date = date('Y-m-d');
        $arHe = array(
            'date' => $date
        );
        if (self::$account && self::$key) {
            $arHe = array(
                'date'    => $date,
                'account' => self::$account,
                'secure'  => md5($date . "&" . self::$key)
            );
        }
        return $arHe;
    }

    protected static function sendToCalculate($data)
    {
        $result = self::client(
            'https://api.cdek.ru/calculator/calculate_price_by_json_request.php',
            array('json' => json_encode($data))
        );
        return $result;
    }

    protected static function sendToCity($data)
    {
        $result = self::client(
            'https://api.cdek.ru/city/getListByTerm/json.php?q=' . urlencode($data)
        );
        return $result;
    }

    protected static function client($where, $data = false)
    {
        if (!$data) {
            $data = array();
        }

        $response = App::getInstance('zoo')->jbhttp->request($where, $data, array(
            // 'headers'   => array('Content-Type' => 'application/x-www-form-urlencoded'),
            // 'cache'     => 0,
            // 'cache_ttl' => self::CACHE_TTL,
            'debug'     => 1,
            'method'    => $data ? 'post' : 'get',
            'response'  => 'full'
        ));

        $result = $response->body;

        return array(
            'code'   => $response->code,
            'result' => $result
        );
    }

    // LANG
    protected static function getLangArray()
    {
        $tanslate = array(
            'YOURCITY'          => JText::_('JBZOO_ELEMENT_SHIPPING_SDEK_WIDGET_YOURCITY'),
            'COURIER'           => JText::_('JBZOO_ELEMENT_SHIPPING_SDEK_WIDGET_COURIER'),
            'PICKUP'            => JText::_('JBZOO_ELEMENT_SHIPPING_SDEK_WIDGET_PICKUP'),
            'TERM'              => JText::_('JBZOO_ELEMENT_SHIPPING_SDEK_WIDGET_TERM'),
            'PRICE'             => JText::_('JBZOO_ELEMENT_SHIPPING_SDEK_WIDGET_PRICE'),
            'DAY'               => JText::_('JBZOO_ELEMENT_SHIPPING_SDEK_WIDGET_DAY'),
            'RUB'               => JText::_('JBZOO_ELEMENT_SHIPPING_SDEK_WIDGET_RUB'),
            'NODELIV'           => JText::_('JBZOO_ELEMENT_SHIPPING_SDEK_WIDGET_NODELIV'),
            'CITYSEARCH'        => JText::_('JBZOO_ELEMENT_SHIPPING_SDEK_WIDGET_CITYSEARCH'),
            'ALL'               => JText::_('JBZOO_ELEMENT_SHIPPING_SDEK_WIDGET_ALL'),
            'PVZ'               => JText::_('JBZOO_ELEMENT_SHIPPING_SDEK_WIDGET_PVZ'),
            'MOSCOW'            => JText::_('JBZOO_ELEMENT_SHIPPING_SDEK_WIDGET_MOSCOW'),
            'RUSSIA'            => JText::_('JBZOO_ELEMENT_SHIPPING_SDEK_WIDGET_RUSSIA'),
            'COUNTING'          => JText::_('JBZOO_ELEMENT_SHIPPING_SDEK_WIDGET_COUNTING'),

            'NO_AVAIL'          => JText::_('JBZOO_ELEMENT_SHIPPING_SDEK_WIDGET_NO_AVAIL'),
            'CHOOSE_TYPE_AVAIL' => JText::_('JBZOO_ELEMENT_SHIPPING_SDEK_WIDGET_CHOOSE_TYPE_AVAIL'),
            'CHOOSE_OTHER_CITY' => JText::_('JBZOO_ELEMENT_SHIPPING_SDEK_WIDGET_CHOOSE_OTHER_CITY'),

            'L_ADDRESS'         => JText::_('JBZOO_ELEMENT_SHIPPING_SDEK_WIDGET_L_ADDRESS'),
            'L_TIME'            => JText::_('JBZOO_ELEMENT_SHIPPING_SDEK_WIDGET_L_TIME'),
            'L_WAY'             => JText::_('JBZOO_ELEMENT_SHIPPING_SDEK_WIDGET_L_WAY'),
            'L_CHOOSE'          => JText::_('JBZOO_ELEMENT_SHIPPING_SDEK_WIDGET_L_CHOOSE'),

            'H_LIST'            => JText::_('JBZOO_ELEMENT_SHIPPING_SDEK_WIDGET_H_LIST'),
            'H_PROFILE'         => JText::_('JBZOO_ELEMENT_SHIPPING_SDEK_WIDGET_H_PROFILE'),
            'H_CASH'            => JText::_('JBZOO_ELEMENT_SHIPPING_SDEK_WIDGET_H_CASH'),
            'H_DRESS'           => JText::_('JBZOO_ELEMENT_SHIPPING_SDEK_WIDGET_H_DRESS'),
            'H_SUPPORT'         => JText::_('JBZOO_ELEMENT_SHIPPING_SDEK_WIDGET_H_SUPPORT'),
            'H_QUESTIONS'       => JText::_('JBZOO_ELEMENT_SHIPPING_SDEK_WIDGET_H_QUESTIONS'),
        );

        return $tanslate;
    }

    // answering
    protected static $answer = false;

    protected static function toAnswer($wat)
    {
        $stucked = array('error');
        if (!is_array($wat)) {
            $wat = array('info' => $wat);
        }
        if (!is_array(self::$answer)) {
            self::$answer = array();
        }
        foreach ($wat as $key => $sign) {
            if (in_array($key, $stucked)) {
                if (!array_key_exists($key, self::$answer)) {
                    self::$answer[$key] = array();
                }
                self::$answer[$key] [] = $sign;
            } else {
                self::$answer[$key] = $sign;
            }
        }
    }

    protected static function printAnswer()
    {
        echo json_encode(self::$answer);
    }
}