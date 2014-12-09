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
 * Class ElementJBFavorite
 */
class ElementJBFavorite extends Element
{
    /**
     * Element constructor
     */
    public function __construct()
    {
        parent::__construct();
        $this->registerCallback('ajaxToggleFavorite');
    }

    /**
     * Checks if the repeatables element's value is set.
     * @param array $params
     * @return bool
     */
    public function hasValue($params = array())
    {
        return true;
    }

    /**
     * Override. Renders the element
     * @param array $params
     * @return string
     */
    public function render($params = array())
    {
        $params = $this->app->data->create($params);

        $isExists = $this->app->jbfavorite->isExists($this->getItem());

        $item        = $this->getItem();
        $ajaxUrl     = $this->app->jbrouter->element($this->identifier, $item->id, 'ajaxToggleFavorite');
        $favoriteUrl = $this->app->jbrouter->favorite($this->config->get('menuitem'), $item->getApplication()->id);

        // render layout
        if ($layout = $this->getLayout()) {
            return $this->renderLayout($layout, array(
                'ajaxUrl'     => $ajaxUrl,
                'favoriteUrl' => $favoriteUrl,
                'isExists'    => $isExists,
            ));
        }

        return null;
    }

    /**
     * Renders the edit form field
     * @return string
     */
    public function edit()
    {
        return null;
    }

    /**
     * Ajax callback for toggle favotite flag
     */
    public function ajaxToggleFavorite()
    {
        $result = array(
            'status' => false,
        );

        $result['status'] = $this->app->jbfavorite->toggleState($this->getItem());

        $this->app->jbajax->send($result, true);
    }
}
