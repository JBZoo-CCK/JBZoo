<?php
/**
 * @package   com_zoo
 * @author    YOOtheme http://www.yootheme.com
 * @copyright Copyright (C) YOOtheme GmbH
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

/**
 * The general String Helper.
 * @package Component.Helpers
 * @since   2.0
 */
class StringHelper extends AppHelper
{

    /**
     * wrapped class
     * @var string
     */
    protected $_class = 'JString';

    /**
     * Map all functions to JString class
     * @param string $method Method name
     * @param array  $args   Method arguments
     * @return mixed
     */
    public function __call($method, $args)
    {
        return $this->_call(array($this->_class, $method), $args);
    }

    /**
     * Truncates the input string.
     * @param string $text            input string
     * @param int    $length          the length of the output string
     * @param string $truncate_string the truncate string
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

            return $this->substr($text, 0, $length) . $truncate_string;
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
        return array(
            '-'   => array('\''),
            'a'   => array('à', 'á', 'â', 'ã', 'ą', 'å', 'a', 'a', 'а'),
            'ae'  => array('ä', 'æ'),
            'c'   => array('c', 'c', 'ç', 'č', 'ć', 'ц'),
            'd'   => array('d', 'd', 'д'),
            'e'   => array('è', 'é', 'ê', 'ë', 'e', 'ě', 'ę', 'е', 'е', 'э'),
            'g'   => array('g', 'ğ', 'г'),
            'i'   => array('ì', 'í', 'î', 'ï', 'ı', 'и'),
            'l'   => array('l', 'l', 'l', 'ł', 'л'),
            'n'   => array('ñ', 'n', 'n', 'ń', 'н'),
            'o'   => array('ò', 'ó', 'ô', 'õ', 'ø', 'o', 'ó', 'ó', 'о'),
            'oe'  => array('ö', 'œ'),
            'r'   => array('r', 'ř', 'р'),
            's'   => array('š', 's', 's', 'ş', 'ś', 'с'),
            't'   => array('t', 't', 't', 'т'),
            'u'   => array('ù', 'ú', 'û', 'u', 'µ', 'у'),
            'ue'  => array('ü'),
            'y'   => array('ÿ', 'ý', 'ы'),
            'z'   => array('ž', 'z', 'z', 'ż', 'ź', 'з'),
            'th'  => array('þ'),
            'dh'  => array('ð'),
            'ss'  => array('ß'),
            'b'   => array('б'),
            'v'   => array('в'),
            'yo'  => array('ё'),
            'zh'  => array('ж'),
            'j'   => array('й'),
            'k'   => array('к'),
            'm'   => array('м'),
            'p'   => array('п'),
            'f'   => array('ф'),
            'h'   => array('х'),
            'ch'  => array('ч'),
            'sh'  => array('ш'),
            'shh' => array('щ'),
            ''    => array('ъ', 'ь', '«', '»'),
            'yu'  => array('ю'),
            'ya'  => array('я'),
        );
    }

    /**
     * Sluggifies the input string.
     * @param string $origString input string
     * @param bool   $forceSafe  Do we have to enforce ASCII instead of UTF8 (default: false)
     * @return string sluggified string
     * @since 2.0
     */
    public function sluggify($origString, $forceSafe = false)
    {
        static $cache = array();

        if (!isset($cache[$origString])) { // performance bug

            $string = $this->strtolower((string)$origString);

            foreach ($this->getTransliteration() as $replace => $keys) {
                foreach ($keys as $search) {
                    $string = JString::str_ireplace($search, $replace, $string);
                }
            }

            $replace = array('#\s+#', '#[^\x{00C0}-\x{00D6}x{00D8}-\x{00F6}\x{00F8}-\x{00FF}\x{0370}-\x{1FFF}\x{4E00}-\x{9FAF}a-z0-9\-]#ui');
            $string  = preg_replace($replace, array('-', ''), $string);
            $string  = preg_replace('#[-]+#u', '-', $string);
            $string  = trim($string, '-');
            $string  = trim($string);

            $cache[$origString] = $string;
        }

        return $cache[$origString];
    }

    /**
     * Apply Joomla text filters based on the user's groups
     * @param  string $string The string to clean
     * @return string         The cleaned string
     */
    public function applyTextFilters($string)
    {
        // Apply the textfilters (let's reuse Joomla's ContentHelper class)
        if (!class_exists('ContentHelper')) {
            require_once JPATH_SITE . '/administrator/components/com_content/helpers/content.php';
        }

        return ContentHelper::filterText((string)$string);
    }

}
