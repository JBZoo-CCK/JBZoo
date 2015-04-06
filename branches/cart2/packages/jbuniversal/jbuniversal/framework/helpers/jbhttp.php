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
 * Class JBHttpHelper
 */
class JBHttpHelper extends AppHelper
{
    const METHOD_GET  = 'get';
    const METHOD_POST = 'post';
    const METHOD_PUT  = 'put';

    const CACHE_GROUP = 'http';

    /**
     * @type array
     */
    protected $_defaultOptions = array(
        'timeout'   => 5,
        'method'    => self::METHOD_GET,
        'headers'   => array(),
        'response'  => 'body', // full, headers, body, code
        'cache'     => 0,
        'cache_ttl' => 60, // in minutes!
        'cache_id'  => '',
        'encoding'  => 'UTF-8', // source encoding. For example, WINDOWS-1251
        'debug'     => 0, // show exceptions
        'certpath'  => null,
        'follow'    => null,
        'driver'    => null, // force using driver (curl, socket, stream)
    );

    /**
     * @type JBCacheHelper
     */
    protected $_jbcache = null;

    /**
     * @param App $app
     */
    public function __construct($app)
    {
        parent::__construct($app);

        $this->_jbcache = $this->app->jbcache;
    }

    /**
     * Simple loading url (if requst doesn't work)
     * @param string $url
     * @param array  $getParams
     * @return string
     */
    public function url($url, $getParams = array())
    {
        if (
            @ini_get('allow_url_fopen')
            && (function_exists('file_get_contents') && is_callable('file_get_contents'))
        ) {
            $url     = $this->app->jbrouter->addParamsToUrl($url, $getParams);
            $context = stream_context_create(array('http' => array(
                'timeout' => $this->_defaultOptions['timeout']
            )));

            $result = @file_get_contents($url, false, $context);

            return $result;
        }

        return null;
    }

    /**
     * @param string $url
     * @param array  $data
     * @param array  $options
     * @return null|string
     * @throws AppException
     */
    public function request($url, $data = array(), $options = array())
    {
        if (!class_exists('JHttpFactory')) {
            return null; // TODO throw exception OR create alt http client
        }

        // get options
        $options = $this->app->data->create(array_merge($this->_defaultOptions, (array)$options));

        // cache handler
        $cacheId = $cacheGroup = $cacheParams = null;
        $isCache = (int)$options->get('cache');
        if ($isCache) {

            $cacheId = array(
                $url,
                (array)$data,
                $options->get('cache_id'),
                (array)$options->get('headers'),
                $options->get('response')
            );

            $cacheParams = array('ttl' => (int)$options->get('cache_ttl'));
            if ($responseBody = $this->_jbcache->get($cacheId, self::CACHE_GROUP, true, $cacheParams)) {
                return $responseBody;
            }
        }

        $httpClient = $this->_getClient($options);

        $result = null;

        try {
            // prepare data
            $method   = $this->app->jbvars->lower($options->get('method'));
            $encoding = $this->app->jbvars->upper($options->get('encoding'));
            $headers  = (array)$options->get('headers');
            $timeout  = (int)$options->get('timeout');
            $data     = is_object($data) ? (array)$data : $data;

            // request
            if (self::METHOD_GET == $method) {
                $url      = $this->app->jbrouter->addParamsToUrl($url, $data);
                $response = $httpClient->get($url, $headers, $timeout);

            } else if (self::METHOD_POST == $method) {
                $response = $httpClient->post($url, $data, $headers, $timeout);

            } else if (self::METHOD_PUT == $method) {
                $response = $httpClient->put($url, $data, $headers, $timeout);

            } else {
                throw new AppException('JBHttpHelper, undefined request method');
            }

            // response type
            if ($options->get('response') == 'body') {

                if ($response->code == 200) {
                    $result = $response->body;

                    if ($encoding != 'UTF-8') {
                        $result = iconv('UTF-8', $encoding . '//TRANSLIT', $result);
                    }

                }

            } else if ($options->get('response') == 'headers') {
                $result = $response->headers;

            } else if ($options->get('response') == 'code') {
                $result = $response->code;

            } else if ($options->get('response') == 'full') {
                $result = $response;

            } else {
                $result = $response->body;
            }

        } catch (RuntimeException $e) {
            if ($options->get('debug')) {
                return $e->getMessage();
            }

            return null;
        }

        if ($isCache) {
            $this->_jbcache->set($cacheId, $result, self::CACHE_GROUP, true, $cacheParams);
        }

        return $result;
    }

    /**
     * @param JSONData $options
     * @return JHttp
     */
    protected function _getClient($options)
    {
        $clientParams = array();

        if ($options->get('certpath')) {
            $clientParams['curl'] = array('certpath' => $options->get('certpath'));
        }

        if ($options->get('follow') !== null) {
            $clientParams['follow_location'] = (bool)$options->get('follow');
        }

        if (class_exists('JRegistry')) { // Old Joomla (2.5.x)
            $clientParams = new JRegistry($clientParams);
        } else if (class_exists('Joomla\Registry\Registry')) { // Joomla 3.x
            $clientParams = new Joomla\Registry\Registry($clientParams);
        }

        $httpClient = JHttpFactory::getHttp($clientParams, $options->get('driver'));

        return $httpClient;
    }

}
