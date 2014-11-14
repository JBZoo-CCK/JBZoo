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
        'timeout'   => 10,
        'method'    => self::METHOD_GET,
        'headers'   => array(),
        'cache'     => 0,
        'cache_ttl' => 60, // in minutes
        'cache_id'  => '',
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
            $cacheId     = array($url, (array)$data, $options->get('cache_id'));
            $cacheParams = array('ttl' => (int)$options->get('cache_ttl'));
            if ($responseBody = $this->_jbcache->get($cacheId, self::CACHE_GROUP, true, $cacheParams)) {
                return $responseBody;
            }
        }

        $httpClient   = JHttpFactory::getHttp();
        $responseBody = null;

        try {
            // prepare data
            $method  = $this->app->jbvars->lower($options->get('method'));
            $headers = (array)$options->get('headers');
            $timeout = (int)$options->get('timeout');
            $data    = (array)$data;

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

            // check and parse error
            if ($response->code == 200) {
                $responseBody = $response->body;

            } else if ($response->code == 404) {
                //throw new AppException('JBHttpHelper, requested to page 404: "' . $url . '")');
            }

        } catch (RuntimeException $e) {
            // return $e->getMessage();
        }

        if ($isCache) {
            $this->_jbcache->set($cacheId, $responseBody, self::CACHE_GROUP, true, $cacheParams);
        }

        return $responseBody;
    }

}
