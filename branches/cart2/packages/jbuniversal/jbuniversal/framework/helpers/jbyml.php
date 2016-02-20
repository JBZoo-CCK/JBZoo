<?php
/**
 * JBZoo App is universal Joomla CCK, application for YooTheme Zoo component
 * @package     jbzoo
 * @version     2.x Pro
 * @author      JBZoo App http://jbzoo.com
 * @copyright   Copyright (C) JBZoo.com,  All rights reserved.
 * @license     http://jbzoo.com/license-pro.php JBZoo Licence
 * @coder       Vitaliy Yanovskiy <joejoker@jbzoo.com>
 */

defined('_JEXEC') or die('Restricted access');

/**
 * Class JBYmlHelper
 */
class JBYmlHelper extends AppHelper
{
    /**
     * @var array
     */
    protected $_country = array(
        "AF" => "Афганистан",
        "AL" => "Албания",
        "DZ" => "Алжир",
        "AS" => "",
        "AD" => "Андорра",
        "AO" => "Ангола",
        "AI" => "Ангилья",
        "AQ" => "",
        "AG" => "Антигуа и Барбуда",
        "AR" => "Аргентина",
        "AM" => "Армения",
        "AW" => "Аруба",
        "AU" => "Австралия",
        "AT" => "Австрия",
        "AZ" => "Азербайджан",
        "BS" => "Багамские острова",
        "BH" => "Бахрейн",
        "BD" => "Бангладеш",
        "BB" => "Барбадос",
        "BY" => "Беларусь",
        "BE" => "Бельгия",
        "BZ" => "Белиз",
        "BJ" => "Бенин",
        "BM" => "Бермудские Острова",
        "BT" => "Бутан",
        "BO" => "Боливия",
        "BA" => "Босния и Герцеговина",
        "BW" => "Ботсвана",
        "BV" => "",
        "BR" => "Бразилия",
        "IO" => "",
        "BN" => "Бруней",
        "BG" => "Болгария",
        "BF" => "Буркина-Фасо",
        "BI" => "Бурунди",
        "KH" => "Камбоджа",
        "CM" => "Камерун",
        "CA" => "Канада",
        "CV" => "Кабо-Верде",
        "KY" => "Каймановы острова",
        "CF" => "Центрально-Африканская Республика ",
        "TD" => "Чад",
        "CL" => "Чили",
        "CN" => "Китай",
        "CX" => "",
        "CC" => "",
        "CO" => "Колумбия",
        "KM" => "Коморские острова ",
        "CG" => "Республика Конго",
        "CD" => "Демократическая Республика Конго",
        "CK" => "Острова Кука",
        "CR" => "Коста-Рика",
        "CI" => "Кот-д'Ивуар",
        "HR" => "Хорватия",
        "CU" => "Куба",
        "CY" => "Кипр",
        "CZ" => "Чехия",
        "DK" => "Дания",
        "DJ" => "Джибути",
        "DM" => "Доминика",
        "DO" => "Доминиканская Республика",
        "EC" => "Эквадор",
        "EG" => "Египет",
        "SV" => "",
        "GQ" => "Экваториальная Гвинея",
        "ER" => "Эритрея",
        "EE" => "Эстония",
        "ET" => "Эфиопия",
        "FK" => "",
        "FO" => "",
        "FJ" => "Фиджи",
        "FI" => "Финляндия",
        "FR" => "Франция",
        "GF" => "Французская Гвиана",
        "PF" => "Французская Полинезия",
        "TF" => "",
        "GA" => "Габон",
        "GM" => "Гамбия",
        "GE" => "Грузия",
        "DE" => "Германия",
        "GH" => "Гана",
        "GI" => "Гибралтар",
        "GR" => "Греция",
        "GL" => "Гренландия",
        "GD" => "Гренада",
        "GP" => "Гваделупа",
        "GU" => "",
        "GT" => "Гватемала",
        "GN" => "Гвинея",
        "GW" => "Гвинея-Бисау",
        "GY" => "Гайана",
        "HT" => "Гаити",
        "HM" => "",
        "VA" => "",
        "HN" => "Гондурас",
        "HK" => "",
        "HU" => "Венгрия",
        "IS" => "Исландия",
        "IN" => "Индия",
        "ID" => "Индонезия",
        "IR" => "Иран",
        "IQ" => "Ирак",
        "IE" => "Ирландия",
        "IL" => "Израиль",
        "IT" => "Италия",
        "JM" => "Ямайка",
        "JP" => "Япония",
        "JO" => "Иордания",
        "KZ" => "Казахстан",
        "KE" => "Кения",
        "KI" => "Кирибати",
        "KP" => "Северная Корея",
        "KR" => "Южная Корея",
        "KW" => "Кувейт",
        "KG" => "Киргизия",
        "LA" => "Лаос",
        "LV" => "Латвия",
        "LB" => "Ливан",
        "LS" => "Лесото",
        "LR" => "Либерия",
        "LY" => "Ливия",
        "LI" => "Лихтенштейн",
        "LT" => "Литва",
        "LU" => "Люксембург",
        "MO" => "Macao",
        "MK" => "Македония",
        "MG" => "Мадагаскар",
        "MW" => "Малави",
        "MY" => "Малайзия",
        "MV" => "Мальдивы",
        "ML" => "Мали",
        "MT" => "Мальта",
        "MH" => "Маршалловы острова ",
        "MQ" => "",
        "MR" => "Мавритания",
        "MU" => "Маврикий",
        "YT" => "Майотта",
        "MX" => "Мексика",
        "FM" => "Федеративные Штаты Микронезии",
        "MD" => "Молдова",
        "MC" => "Монако",
        "MN" => "Монголия",
        "ME" => "Черногория",
        "MS" => "",
        "MA" => "Марокко",
        "MZ" => "Мозамбик",
        "MM" => "Мьянма",
        "NA" => "Намибия",
        "NR" => "Науру",
        "NP" => "Непал",
        "NL" => "Нидерланды",
        "AN" => "Нидерландские Антильские острова",
        "NC" => "Новая Каледония",
        "NZ" => "Новая Зеландия",
        "NI" => "Никарагуа",
        "NE" => "Нигер",
        "NG" => "Нигерия",
        "NU" => "",
        "NF" => "",
        "MP" => "",
        "NO" => "Норвегия",
        "OM" => "Оман",
        "PK" => "Пакистан",
        "PW" => "Палау",
        "PS" => "",
        "PA" => "Панама",
        "PG" => "Папуа - Новая Гвинея",
        "PY" => "Парагвай",
        "PE" => "Перу",
        "PH" => "Филиппины",
        "PN" => "",
        "PL" => "Польша",
        "PT" => "Португалия",
        "PR" => "",
        "QA" => "Катар",
        "RE" => "Реюньон",
        "RO" => "Румыния",
        "RU" => "Россия",
        "RW" => "Руанда",
        "SH" => "",
        "KN" => "Сент-Китс и Невис",
        "LC" => "Сент-Люсия",
        "PM" => "",
        "VC" => "Сент-Винсент и Гренадины",
        "WS" => "Самоа",
        "SM" => "Сан-Марино",
        "ST" => "Сан-Томе и Принсипи",
        "SA" => "Саудовская Аравия",
        "SN" => "Сенегал",
        "RS" => "Сербия",
        "SC" => "Сейшельские острова",
        "SL" => "Сьерра-Леоне",
        "SG" => "Сингапур (страна)",
        "SK" => "Словакия",
        "SI" => "Словения",
        "SB" => "",
        "SO" => "Сомали",
        "ZA" => "ЮАР",
        "GS" => "",
        "ES" => "Испания",
        "LK" => "Шри-Ланка",
        "SD" => "Судан",
        "SR" => "Суринам",
        "SJ" => "",
        "SZ" => "Свазиленд",
        "SE" => "Швеция",
        "CH" => "Швейцария",
        "SY" => "Сирия",
        "TW" => "",
        "TJ" => "Таджикистан",
        "TZ" => "Танзания",
        "TH" => "Таиланд",
        "TL" => "Восточный Тимор",
        "TG" => "Того",
        "TK" => "",
        "TO" => "Тонга",
        "TT" => "Тринидад и Тобаго",
        "TN" => "Тунис",
        "TR" => "Турция",
        "TM" => "Туркмения",
        "TC" => "Тёркс и Кайкос",
        "TV" => "Тувалу",
        "UG" => "Уганда",
        "UA" => "Украина",
        "AE" => "Объединённые Арабские Эмираты",
        "GB" => "Великобритания",
        "US" => "США",
        "UM" => "",
        "UY" => "Уругвай",
        "UZ" => "Узбекистан",
        "VU" => "Вануату",
        "VE" => "Венесуэла",
        "VN" => "Вьетнам",
        "VG" => "Британские Виргинские острова",
        "VI" => "Американские Виргинские острова",
        "WF" => "",
        "EH" => "Западная Сахара",
        "YE" => "Йемен",
        "ZM" => "Замбия",
        "ZW" => "Зимбабве",
    );

    /**
     * @var null
     */
    protected $_appParams = null;

    /**
     * @var null
     */
    protected $_apps = null;

    /**
     * @var null
     */
    protected $_count = null;

    /**
     * Action init yml export
     */
    public function init()
    {
        // Init vars
        $this->_appParams = $this->app->jbconfig->getList('config.yml');
        $appList          = $this->_appParams->get('app_list');

        if (!empty($appList)) {
            $this->_apps = $this->app->table->application->all(array(
                'conditions' => array(
                    'id IN (' . implode(',', $appList) . ')'),
            ));
        } else {
            $this->_apps = '';
        }

        $this->_count = $this->app->jbsession->get('ymlCount', 'yml');
    }

    /**
     * Action Cet params Market
     * @return array
     */
    protected function _getMarketParams()
    {
        $textCurrencyRate = array();
        $siteUrl          = $this->_appParams->get('site_url');
        $siteName         = $this->_appParams->get('site_name');
        $companyName      = $this->_appParams->get('company_name');
        $supportCurrency  = array('RUB', 'USD', 'BYR', 'KZT', 'EUR', 'UAH');
        $currency         = $this->_appParams->get('currency', 'RUB');
        $currencyRate     = $this->_appParams->get('currency_rate', 'default');

        if (!empty($siteUrl)) {
            $site_url = $this->replaceSpecial($this->_appParams->get('site_url', ''));
        } else {
            $site_url = $this->replaceSpecial(JURI::root());
        }

        if (!empty($siteName)) {
            $site_name = $this->replaceSpecial($this->_appParams->get('site_name', ''));
        } else {
            $site_name = $this->replaceSpecial(JFactory::getConfig()->get('sitename'));
        }

        if (!empty($companyName)) {
            $company_name = $this->replaceSpecial($this->_appParams->get('company_name', ''));
        } else {
            $company_name = $this->replaceSpecial(JFactory::getConfig()->get('sitename'));
        }

        foreach ($this->app->jbmoney->getCurrencyList() as $key => $value) {

            $key = strtoupper($key);

            if ($currencyRate == 'default') {
                if (in_array($key, $supportCurrency)) {
                    $textCurrencyRate[$key] = str_replace(',', '.', $this->app->jbmoney->convert($key, $currency, 1));
                }

            } else {
                if (in_array($key, $supportCurrency)) {
                    $textCurrencyRate[$key] = $this->_appParams->get('currency_rate');
                }
            }
        }

        if ($currencyRate != 'default') {
            $defaultCur = JBModelConfig::model()->getGroup('cart.config')->get('default_currency', 'RUB');
            $defaultCur = strtoupper($defaultCur);

            $textCurrencyRate[$defaultCur] = 1;
        }

        if (isset($textCurrencyRate['RUB'])) {
            $textCurrencyRate['RUR'] = $textCurrencyRate['RUB'];
            unset($textCurrencyRate['RUB']);
        }

        $categories = $this->app->table->category->all(array(
            'conditions' => array(
                'application_id IN (' . implode(',', $this->_appParams->get('app_list')) . ')'),
        ));

        return array(
            'site_url'      => $site_url,
            'site_name'     => $site_name,
            'company_name'  => $company_name,
            'currency_rate' => $textCurrencyRate,
            'categories'    => $categories,
        );
    }

    /**
     * Action write Start File
     */
    public function renderStart()
    {
        $this->_count = null;
        $marketParams = $this->_getMarketParams();
        $string       = '<?xml version="1.0" encoding="utf-8"?>' . "\n" . '<!DOCTYPE yml_catalog SYSTEM "shops.dtd">' . "\n" . '<yml_catalog date="' . JHTML::_("date", "now", JText::_("Y-m-d H:i")) . '">
        <shop>
            <name>' . $marketParams['site_name'] . '</name>
            <company>' . $marketParams['company_name'] . '</company>
            <url>' . $marketParams['site_url'] . '</url>
            <currencies>' . "\n";

        foreach ($marketParams['currency_rate'] as $key => $value) {
            $string .= '<currency id="' . $key . '" rate="' . $this->replaceSpecial($value) . '"/>' . "\n";
        }
        $string .= '</currencies><categories>' . "\n";

        foreach ($marketParams['categories'] as $category) {
            if (!empty($category->parent)) {
                $string .= '<category id="' . $category->id . '" parentId="' . $category->parent . '">' . $this->replaceSpecial($category->name) . '</category>' . "\n";
            } else {
                $string .= '<category id="' . $category->id . '">' . $this->replaceSpecial($category->name) . '</category>' . "\n";
            }
        }
        $string .= '</categories>' . "\n" . '<offers>' . "\n";

        $this->_writeToFile($string, true);
    }


    /**
     * @param $offset
     * @param $limit
     * @throws AppException
     */
    public function exportItems($offset, $limit)
    {
        $types = $this->_appParams->get('type_list');
        $items = JBModelItem::model()->getList(
            $this->_appParams->get('app_list'),
            null,
            $types,
            array(
                'limit'     => array($offset, $limit),
                'published' => 1,
            )
        );

        $priceOld = $price = $categoryId = $currencyId = $available = $picture = $link = array();

        foreach ($items as $key => $item) {
            $offer    = false;
            $elements = $item->getElements();
            foreach ($elements as $element) {

                $data = $element->getElementData();

                if ($element->config->type == 'jbpriceadvance') {

                    $indexData        = $element->getIndexData();
                    $price[$key]      = $this->replaceSpecial($indexData[$item->id]['total']);
                    $currencyId[$key] = $this->replaceSpecial($indexData[$item->id]['currency']);

                    // get available
                    $balance = $indexData[$item->id]['balance'];
                    if ($balance == 0) {
                        $available[$key] = 'false';
                    } else {
                        $available[$key] = 'true';
                    }

                    $offer = true;

                } elseif ($element->config->type == 'jbprice') {
                    $data             = $element->current();
                    $price[$key]      = $data['value'];
                    $currencyId[$key] = $element->config->currency;
                    $available[$key]  = (isset($data['in_stock']) && $data['in_stock']) ? 'true' : 'false';
                    $offer            = true;

                } elseif ($element->config->type == 'jbpriceplain' || $element->config->type == 'jbpricecalc') {

                    $prices   = $element->getList()->getTotal();
                    $oldPrice = $element->getList()->getPrice();
                    $balance  = $element->getList()->current()->getValue(true, '_balance');

                    $priceCur         = $prices->cur();
                    $price[$key]      = $prices->val();
                    $currencyId[$key] = $priceCur;

                    $priceOld[$key] = 0;
                    if ($prices->compare($oldPrice, '<')) {
                        $priceOld[$key] = $oldPrice->val($priceCur);
                    }

                    if ($balance) {
                        if ($balance == 0) {
                            $available[$key] = 'false';

                        } elseif ($balance == -1 || $balance > 0) {
                            $available[$key] = 'true';

                        } else {
                            $available[$key] = 'false';
                        }

                    } else {
                        $available[$key] = 'false';
                    }

                    $offer = true;
                }

                // get image paths
                if ($element->config->type == 'image') {
                    $picture[$key] = $this->replaceSpecial(JURI::root() . str_replace('\\', '/', $data->get('file')));
                }

                // get jbimage paths
                if ($element->config->type == 'jbimage') {

                    $picture[$key] = array();

                    $imageData = $element->data();
                    $limit     = 10;

                    foreach ((array)$imageData as $i => $row) {
                        if (isset($row['file']) && $row['file']) {
                            $picture[$key][] = $this->replaceSpecial(JURI::root() . str_replace('\\', '/', $row['file']));
                        }

                        if ($limit == $i + 1) {
                            break;
                        }
                    }

                    $picture[$key] = array_unique($picture[$key]);
                }

                // get countries
                if ($element->config->type == 'country') {
                    $countryData = $data->get('country');
                    if (isset($countryData[0]) && array_key_exists($countryData[0], $this->_country)) {
                        $country[$key] = $this->_country[$countryData[0]];
                    }
                }

                if (isset($currencyId[$key])) {
                    $currencyId[$key] = strtoupper($currencyId[$key]);

                    if ($currencyId[$key] == 'RUB') {
                        $currencyId[$key] = 'RUR';
                    }
                }

            }

            if (!$offer) {
                $filePath = $this->getPath();

                if (JFile::exists($filePath)) {
                    JFile::delete($filePath);
                }

                throw new AppException(JText::_('JBZOO_YML_NOT_PRODUCT_OFFER_FILE') . $item->getType()->getName());
            }

            // get item link
            $link[$key] = $this->replaceSpecial($this->app->jbrouter->externalItem($item));

            // get category Id
            if ($item->getPrimaryCategoryId() != null) {
                $categoryId[$key] = $item->getPrimaryCategoryId();
            }
        }

        $itemParams = array(
            'price'      => $price,
            'priceOld'   => $priceOld,
            'pricesOld'   => $priceOld, // only for old versions
            'categoryId' => $categoryId,
            'currencyId' => $currencyId,
            'available'  => $available,
            'picture'    => $picture,
            'link'       => $link,
        );

        if (isset($country) && !empty($country)) {
            $itemParams['country'] = $country;
        }


        $appPaths = array(
            $this->app->path->path('component.site:'),
            $this->app->path->path('jbtmpl:') . '/catalog',
        );

        $appList = $this->app->table->application->all(array(
            'conditions' => array('id IN (' . implode(',', $this->_appParams->get('app_list')) . ')'),
        ));

        if (!empty($appList)) {
            foreach ($appList as $oneApp) {
                $appPaths[] = $this->app->path->path('jbtmpl:') . '/' . $oneApp->getTemplate()->name;
            }
        }

        $renderer = $this->app->renderer->create('item')->addPath($appPaths);

        $strItems = "";
        foreach ($items as $item) {
            if ($renderer->pathExists('item/' . $item->type . '/ymlexport.php') || $renderer->pathExists('item/ymlexport.php')) {

                $tpl = 'item.' . $item->type . '.ymlexport';

                if (!$renderer->pathExists('item/' . $item->type . '/ymlexport.php')) {
                    $tpl = 'item.ymlexport';
                }

                $tmpStrItems = $renderer->render($tpl, array(
                        'view'        => $this,
                        'item'        => $item,
                        'item_params' => $itemParams,
                    )
                );

                $tmpStrItems = JString::trim($tmpStrItems);

                if (!empty($tmpStrItems)) {
                    $this->_count++;
                }

                $strItems .= $tmpStrItems;

            } else {
                throw new AppException(JText::_('JBZOO_YML_NOT_EXISTS_TEMPLATE') . ' ' . $item->getType()->getName());
            }
        }
        $this->app->jbsession->set('ymlCount', $this->_count, 'yml');
        $this->_writeToFile($strItems, false);
    }

    /**
     * Action write finish file
     */
    public function renderFinish()
    {
        $this->_writeToFile('</offers></shop></yml_catalog>', false);
    }


    /**
     * @param      $resource
     * @param bool $start
     * @throws AppException
     */
    protected function _writeToFile($resource, $start = true)
    {
        $filePath = $this->getPath();
        $dir      = $this->_appParams->get('file_path', 'images');

        if ($start && JFile::exists($filePath)) {
            JFile::delete($filePath);
        }

        if (!JFile::exists($filePath)) {
            JFolder::create(JPATH_ROOT . DS . $dir);
        }

        $handle = fopen($filePath, 'a');

        if (is_writable($filePath)) {

            fwrite($handle, $resource);

        } else {
            throw new AppException(JText::_('JBZOO_YML_NOT_WRITABLE_FILE'));
        }
        fclose($handle);
    }


    /**
     * @param bool $path
     * @return string
     */
    public function getPath($path = true)
    {
        if ($path) {
            $tmpPath  = $this->_appParams->get('file_path', 'images');
            $tmpPath  = JPATH_ROOT . DS . $tmpPath;
            $fileName = $this->_appParams->get('file_name', 'yml');
            $filePath = JPath::clean($tmpPath . DS . $fileName . '.xml');
        } else {
            $tmpPath  = trim($this->_appParams->get('file_path', 'images'), '/');
            $filePath = JUri::root() . $tmpPath . '/' . $this->_appParams->get('file_name', 'yml') . '.xml';
        }

        return $filePath;
    }

    /**
     * Action Replace special simbols
     * @param $text
     * @return string
     */
    public function replaceSpecial($text)
    {
        return trim(addslashes(htmlspecialchars(strip_tags($text))));
    }

    /**
     * @return int|boolean
     */
    public function getTotal()
    {
        $types = $this->_appParams->get('type_list');
        $appId = $this->_appParams->get('app_list');

        if (empty($types) || empty($appId)) {
            return false;
        }

        return JBModelItem::model()->getTotal($appId, $types);
    }
}