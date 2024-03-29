<?php
use Joomla\String\StringHelper;
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
 * Class JBItemHelper
 */
class JBItemHelper extends AppHelper
{
    /**
     * @var string
     */
    protected $_defaultAlign = 'left';

    /**
     * Get align for media position for item
     * @param Item   $item
     * @param string $layout
     * @return string
     */
    public function getMediaAlign(Item $item, $layout)
    {
        $paramName = str_replace('.' . $item->type . '.', '.', $layout);
        $paramName = str_replace('.', '_', $paramName);
        $paramName = 'template.' . $paramName . '_image_align';

        $align = $item->params->get($paramName, false);

        if ($align === false) {
            $align = $item->getApplication()->params->get('global.' . $paramName, $this->_defaultAlign);
        }

        return $align;
    }

    /**
     * Render HTML image form item by elementId
     * @param Item $item
     * @param      $elementId
     * @param bool $isLink
     * @return string|null
     */
    public function renderImageFromItem($item, $elementId, $isLink = false)
    {
        if (empty($elementId) || empty($item)) {
            return null;
        }

        if (is_array($elementId)) {
            $result = '';
            foreach ($elementId as $elementIdrow) {
                $result .= PHP_EOL . $this->renderImageFromItem($item, $elementIdrow, $isLink);
            }

            return $result;
        }

        $element = $item->getElement($elementId);
        if (StringHelper::strtolower(get_class($element)) == 'elementjbimage') {

            return $element->render(array(
                'width'    => 75,
                'height'   => 75,
                'template' => $isLink ? 'itemlink' : 'default',
                'display'  => 'first',
            ));

        } else if (StringHelper::strtolower(get_class($element)) == 'elementimage') {

            return $element->render(array(
                'width'  => 75,
                'height' => 75,
            ));

        } else if (StringHelper::strtolower(get_class($element)) == 'elementimagepro') {

            return $element->render(array(
                'specific._width'  => 75,
                'specific._height' => 75,
            ));
        }

        return null;
    }

}