<?php
/**
 * JBZoo App is universal Joomla CCK, application for YooTheme Zoo component
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
 * Class JBCartElementPriceImage
 */
class JBCartElementPriceImage extends JBCartElementPrice
{
    const IMAGE_EXISTS    = 1;
    const IMAGE_NO_EXISTS = 0;

    /**
     * Check if element has value
     * @param array $params
     * @return bool
     */
    public function hasValue($params = array())
    {
        $img = $this->config->get('image', null);
        if (!empty($img)) {
            return true;
        }

        return true;
    }

    /**
     * Get elements search data
     * @return null
     */
    public function getSearchData()
    {
        $value    = JString::trim($this->getValue());
        $isExists = !empty($value) && JFile::exists(JPATH_ROOT . '/' . $value);

        if ($isExists) {
            return self::IMAGE_EXISTS;
        }

        return self::IMAGE_NO_EXISTS;
    }

    /**
     * @return mixed|null|string
     */
    public function edit()
    {
        if ($layout = $this->getLayout('edit.php')) {
            $this->app->jbassets->media();

            return self::renderEditLayout($layout, array(
                'value'  => $this->get('value', ''),
                'unique' => $this->htmlId(true)
            ));
        }

        return null;
    }

    /**
     * @param array $params
     * @return array|mixed|null|string
     */
    public function render($params = array())
    {
        $unique = $this->unique();

        if ($layout = $this->getLayout()) {
            return self::renderLayout($layout, array(
                'element' => $unique
            ));
        }

        return null;
    }

    /**
     * Get unique class
     * @return string
     */
    public function unique()
    {
        $image  = $this->config->get('image');
        $unique = $this->layout . '_' . $this->item_id;

        if (empty($image)) {
            return $unique;
        }

        return $unique . '_' . $image;
    }

    /**
     * @param $image
     * @param $params
     * @return JSONData|string
     */
    public function getImage($image, $params = array())
    {
        if (empty($image)) {
            return $image;
        }

        $jbImage = $this->app->jbimage;
        if (is_array($image)) {
            $image = $image['value'];
        }

        if (!$params) {
            return false;
        }

        $width  = $params->get('width');
        $height = $params->get('height');

        $img = new stdClass();

        $url = $jbImage->getUrl($image);
        if ($width || $height) {
            $url = $jbImage->resize($image, $width, $height)->url;
        }

        $width_pop  = $params->get('width_popup');
        $height_pop = $params->get('height_popup');

        $img->image = $url;
        if ($width_pop || $height_pop) {
            $url = $jbImage->resize($image, $width_pop, $height_pop)->url;
        }
        $img->pop_up = $url;

        return !empty($img) ? $img : null;
    }

    /**
     * Get params for widget
     * @param array $params
     * @return array
     */
    public function interfaceParams($params = array())
    {
        $path = $this->getValue();

        return array(
            'related' => $this->unique(),
            'image'   => $this->getImage($path, $params)
        );
    }

    /**
     * Returns data when variant changes
     * @param array $params
     * @return null
     */
    public function renderAjax($params = array())
    {
        $path = $this->getValue();

        return $this->getImage($path, $params);
    }

    /**
     * Load elements css/js assets
     * @return $this
     */
    public function loadAssets()
    {
        $this->js(array(
            'cart-elements:price/image/assets/js/image.js',
            'jbassets:js/widget/media.js',
        ));

        return parent::loadAssets();
    }

}
