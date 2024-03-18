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
use Joomla\CMS\Language\Text;

defined('_JEXEC') or die('Restricted access');

?>

<div id="<?php echo $id; ?>" class="jsUpload jbvideo" data-count="<?php echo $videos ? count($videos) : 0; ?>">

    <div class="jsUploadZone jbvideo__upload">
        <span class="jbvideo__upload-text"><?php echo Text::_('JBZOO_JBVIDEO_EDIT_UPLOAD_TEXT_1'); ?></span>
        <span class="jbvideo__upload-input">
            <input class="jsUploadInput" type="file" name="<?php echo $this->identifier; ?>-jbvideos[]" multiple>
            <span class="jbvideo__upload-input-link"><?php echo Text::_('JBZOO_JBVIDEO_EDIT_UPLOAD_TEXT_2'); ?></span>
        </span>
    </div>

    <div class="jbvideo__from-server clearfix">
        <button type="button" class="jsServerUpload btn btn-small" data-id="<?php echo $this->identifier; ?>"><i
                class="icon-plus"></i> <?php echo Text::_('JBZOO_JBVIDEO_EDIT_UPLOAD_FROM_SERVER'); ?></button>
    </div>

    <div class="jsUploadAlert jbvideo__alert" style="display: none;"></div>

    <div class="jsUploadProgress jbvideo__progress" style="display: none;">
        <div class="jbvideo__progress-bar"></div>
    </div>

    <ul class="jsUploadFiles jbvideo__files">
        <?php
        if (!empty($videos))
        {
            foreach ($videos as $key => $video)
            {
                $video = array(
                    'file'   => isset($video['file']) ? $video['file'] : '',
                );

                if ($this->isFileExists($video['file']))
                {
                    $file     = $video['file'];
                    $fileName = basename($file);

                    $deleteType = $this->config->get('delete_type', 'simple');

                    if (strpos($video['file'], $this->config->get('upload_directory')) === false)
                    {
                        $deleteType = 'simple';
                    } ?>

                    <li class="jbvideo__file-item">
                        <div class="jbvideo__file-item-body">
                            <div class="jbvideo__file-item-teaser">
                                <video src="/<?php echo $file; ?>" controls></video>
                                <a href="#" data-type="<?php echo $deleteType; ?>"
                                   class="jsUploadDelete jbvideo__file-item-delete jbvideo__file-item-delete--<?php echo $deleteType; ?>"></a>
                            </div>

                            <p>
                                <i class="jbvideo__file-item-icon jbvideo__file-item-icon--check"></i> <?php echo $fileName; ?>
                            </p>

                            <input style="display: none;" name="<?php echo $this->getControlName('file', $key); ?>"
                                   type="text" value="<?php echo $file; ?>">
                        </div>
                    </li>
                    <?php
                }
            }
        }
        ?>
    </ul>
</div>


<?php
// Init Upload
$this->app->jbassets->initUpload($id, $options);
?>
