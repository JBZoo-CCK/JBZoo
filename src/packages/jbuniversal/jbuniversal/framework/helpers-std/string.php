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

use Joomla\CMS\Application\ApplicationHelper;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Filter\OutputFilter;

/**
 * The general String Helper.
 *
 * @package Component.Helpers
 * @since 2.0
 */
class StringHelper extends AppHelper {

	/**
	 * Map all functions to StringHelper class
	 *
	 * @param string $method Method name
	 * @param array $args Method arguments
	 *
	 * @return mixed
	 */
	public function __call($method, $args) {
		return $this->_call(array(Joomla\String\StringHelper::class, $method), $args);
	}

	/**
	 * Truncates the input string.
	 *
	 * @param string $text input string
	 * @param int $length the length of the output string
	 * @param string $truncate_string the truncate string
	 *
	 * @return string The truncated string
	 * @since 2.0
	 */
	public function truncate($text, $length = 30, $truncate_string = '...')
	{
		if ($text == '') {
			return '';
		}

		if ($this->strlen($text) > $length) {
			$length -= min($length, strlen($truncate_string));
			$text = preg_replace('/\s+?(\S+)?$/', '', substr($text, 0, $length + 1));

			return $this->substr($text, 0, $length).$truncate_string;
		} else {
			return $text;
		}
	}

	/**
     * Get the transliteration array
     * @return array Transliteration
     */
    public function getTransliteration()
    {
        return [
            '-'    => ['\'', '/', ' ', ' / '],
            'a'    => ['à', 'á', 'â', 'ã', 'ą', 'å', 'a', 'a', 'а'],
            'ae'   => ['ä', 'æ'],
            'c'    => ['c', 'c', 'ç', 'č', 'ć', 'ц'],
            'd'    => ['d', 'd', 'д'],
            'e'    => ['è', 'é', 'ê', 'ë', 'e', 'ě', 'ę', 'е', 'е', 'э', 'є'],
            'g'    => ['g', 'ğ', 'г', 'ґ'],
            'i'    => ['ì', 'í', 'î', 'ï', 'ı', 'и', 'і', 'ї'],
            'l'    => ['l', 'l', 'l', 'ł', 'л'],
            'n'    => ['ñ', 'n', 'n', 'ń', 'н'],
            'o'    => ['ò', 'ó', 'ô', 'õ', 'ø', 'o', 'ó', 'ó', 'о'],
            'oe'   => ['ö', 'œ'],
            'r'    => ['r', 'ř', 'р'],
            's'    => ['š', 's', 's', 'ş', 'ś', 'с'],
            't'    => ['t', 't', 't', 'т'],
            'u'    => ['ù', 'ú', 'û', 'u', 'µ', 'у'],
            'ue'   => ['ü'],
            'y'    => ['ÿ', 'ý', 'ы'],
            'z'    => ['ž', 'z', 'z', 'ż', 'ź', 'з'],
            'th'   => ['þ'],
            'dh'   => ['ð'],
            'ss'   => ['ß'],
            'b'    => ['б'],
            'v'    => ['в'],
            'yo'   => ['ё'],
            'zh'   => ['ж'],
            'j'    => ['й'],
            'k'    => ['к'],
            'm'    => ['м'],
            'p'    => ['п'],
            'f'    => ['ф'],
            'h'    => ['х'],
            'ch'   => ['ч'],
            'sh'   => ['ш'],
            'shch' => ['щ'],
            ''     => [
                'ъ',
                'ь',
                '«',
                '»',
                '@',
                '#',
                '!',
                '$',
                '%',
                '^',
                '&',
                '*',
                '?',
                '=',
                '+',
                '~',
                '"',
                ':',
                ';',
                '.',
                ',',
                '(',
                ')',
                '№'
            ],
            'yu'   => ['ю'],
            'ya'   => ['я'],
        ];
    }

	/**
	 * Sluggifies the input string.
	 *
	 * @param string $string 		input string
	 * @param bool   $force_safe 	Do we have to enforce ASCII instead of UTF8 (default: false)
	 *
	 * @return string sluggified string
	 * @since 2.0
	 */
	public function sluggify($origString, $forceSafe = false)
    {
        static $cache = [];

        if (!isset($cache[$origString])) { // performance bug

            $string = $this->strtolower((string)$origString);

            foreach ($this->getTransliteration() as $replace => $keys) {
                foreach ($keys as $search) {
                    $string = $this->str_ireplace($search, $replace, $string);
                }
            }

            $replace = [
                '#\s+#',
                '#[^\x{00C0}-\x{00D6}x{00D8}-\x{00F6}\x{00F8}-\x{00FF}\x{0370}-\x{1FFF}\x{4E00}-\x{9FAF}a-z0-9\-]#ui'
            ];
            $string = preg_replace($replace, ['-', ''], $string);
            $string = preg_replace('#[-]+#u', '-', $string);
            $string = trim($string, '-');
            $string = trim($string);

            $cache[$origString] = $string;
        }

        return $cache[$origString];
    }

    /**
     * Apply Joomla text filters based on the user's groups
     *
     * @param  string|array $string The string to clean
     *
     * @return string|array The cleaned string
     */
    public function applyTextFilters($string) {
        if (is_array($string)) {
            foreach ($string as $k => $v) {
                $string[$k] = $this->applyTextFilters($v);
            }
            return $string;
        }

        return ComponentHelper::filterText((string) $string);
    }

	/**
	 * Converts string to camel case (https://en.wikipedia.org/wiki/Camel_case).
	 *
	 * @param string|string[] $string
	 * @param bool            $upper
	 *
	 * @return string
	 */
	public function camelCase($string, $upper = false)
	{
		$string = join(' ', (array) $string);
		$string = str_replace(array('-', '_'), ' ', $string);
		$string = str_replace(' ', '', ucwords($string));

		return $upper ? $string : lcfirst($string);
	}
}
