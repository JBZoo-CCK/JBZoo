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
 * Class JBRendererHelper
 */
class JBRendererHelper extends AppHelper
{
    /**
     * @param $rendererType
     * @param string $paths
     * @return PositionRenderer
     */
    public function create($rendererType, $paths = null)
    {
        $rendererType = trim(strtolower($rendererType));
        $renderer     = $this->app->renderer->create($rendererType);

        $renderer->addPath(array(
            $this->app->path->path('jbtmpl:catalog')
        ));

        if ($paths) {
            $renderer->addPath($paths);
        }

        return $renderer;
    }

}
