<?php
declare(strict_types=1);

use Joomla\Registry\Registry;

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
 * Class JBZooItemType
 */
abstract class JBZooItemType
{
    /**
     * @var App|string
     */
    public App|string $app;

    /**
     * @var Registry|null
     */
    protected ?Registry $_params = null;

    /**
     * Init Zoo
     * @param Registry $params
     */
    public function __construct(Registry $params)
    {
        $this->app = App::getInstance('zoo');
        $this->_params = $params;
    }

    /**
     * @return mixed
     */
    abstract function getItems();
}
