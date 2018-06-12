<?php
/**
 * JBZoo | Application for ZOO
 *
 * This file is part of the JBZoo CCK package.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package    Application for ZOO
 * @license    GPL-2.0
 * @copyright  Copyright (C) JBZoo.com, All rights reserved.
 * @link       https://github.com/JBZoo/JBZoo
 */

namespace JBZoo\PHPUnit;

/**
 * Class CodeStyleTest
 * @package JBZoo\PHPUnit
 */
class CodeStyleTest extends Codestyle
{
    protected $_packageVendor  = 'JBZoo |';
    protected $_packageName    = 'Application for ZOO';
    protected $_packageLicense = 'GPL-2.0';
    protected $_packageLink    = 'https://github.com/JBZoo/JBZoo';
    protected $_packageAuthor  = 'Denis Smetannikov';

    /**
     * @inheritDoc
     */
    protected $_excludePaths = [
        '.git',
        '.idea',
        'bin',
        'build',
        'vendor',
        'temp',
        'tmp',
    ];
}
