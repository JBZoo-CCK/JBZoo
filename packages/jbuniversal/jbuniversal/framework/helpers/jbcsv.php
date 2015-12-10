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
 * Class JBCSVHelper
 */
class JBCSVHelper extends AppHelper
{
    /**
     * @var array|JBModelConfig
     */
    protected $_config = array();

    /**
     * @param App $app
     */
    public function __construct($app)
    {
        parent::__construct($app);

        $this->_config = JBModelConfig::model();
    }

    /**
     * @param       $data
     * @param       $file
     * @param array $maxima
     * @param bool  $addHeader
     * @return bool|string
     */
    public function toFile($data, $file, array $maxima = null, $addHeader = true)
    {
        if (empty($data)) {
            return false;
        }

        // use maxima to pad arrays
        if (!empty($maxima)) {
            foreach ($maxima as $key => $num) {
                foreach (array_keys($data) as $i) {
                    $data[$i][$key] = array_pad((array)$data[$i][$key], $num, '');
                }
            }
        }

        return $this->_createFile($data, $file, $addHeader);
    }

    /**
     * From file
     * @param          $file
     * @param JSONData $options
     * @return array
     */
    public function fromFile($file, $options)
    {
        $lines = array();
        if (($handle = fopen($file, "r")) !== false) {

            while (($data = fgetcsv($handle, 0,
                    $options->get('separator', ','),
                    $options->get('enclosure', '"'))
                ) !== false) {

                $lines[] = $data;
            }

            fclose($handle);
        }

        return $lines;
    }

    /**
     * From file
     * @param          $file
     * @param JSONData $options
     * @param int      $start
     * @param int      $step
     * @return array
     */
    public function getLinesfromFile($file, $options, $start, $step)
    {
        $lines  = array();
        $finish = (int)$start + (int)$step;

        $i = 0;

        if (($handle = fopen($file, "r")) !== false) {

            while (($data = fgetcsv($handle, 0, $options->get('separator', ','), $options->get('enclosure', '"'))) !== false) {
                $i++;
                if ($i > $finish) {
                    break;
                }
                if ($start >= $i) {
                    continue;
                }
                $lines[] = $data;
            }

            fclose($handle);
        }

        return $lines;
    }

    /**
     * Add header
     * @param $data
     * @return mixed
     */
    protected function _addHeader($data)
    {
        array_unshift($data, array());
        foreach ($data[1] as $key => $value) {
            $num     = is_array($value) ? count($value) : 1;
            $data[0] = array_merge($data[0], array_fill(0, max(1, $num), $key));
        }

        return $data;
    }

    /**
     * Create CSV file from $data
     * @param array  $data
     * @param string $filename
     * @param bool   $addHeader
     * @return string
     * @throws AppException
     */
    protected function _createFile($data, $filename, $addHeader = true)
    {
        $file    = $this->app->jbpath->sysPath('tmp', '/' . JBExportHelper::EXPORT_PATH . '/' . $filename . '.csv');
        $config  = $this->_config->getGroup('export');
        $dirName = dirname($file);

        if (!JFolder::exists($dirName)) {
            JFolder::create($dirName);
        }

        if (!JFile::exists($file) && $addHeader) {
            $data = $this->_addHeader($data);
        }

        if (($handle = fopen($file, "a")) !== false) {

            foreach ($data as $row) {
                fputcsv($handle,
                    $this->app->data->create($row)->flattenRecursive(),
                    $config->get('separator', ','),
                    $config->get('enclosure', '"')
                );
            }

            fclose($handle);

        } else {
            throw new AppException(sprintf('Unable to write to file %s.', $file));
        }

        return $file;
    }

}
