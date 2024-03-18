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

<div id="<?php echo $id; ?>" class="jsUpload jbimage" data-count="<?php echo $images ? count($images) : 0; ?>">

    <div class="jsUploadZone jbimage__upload">
        <span class="jbimage__upload-text"><?php echo Text::_('JBZOO_JBIMAGE_EDIT_UPLOAD_TEXT_1'); ?></span>
        <span class="jbimage__upload-input">
            <input class="jsUploadInput" type="file" name="<?php echo $this->identifier; ?>-jbimages[]" multiple>
            <span class="jbimage__upload-input-link"><?php echo Text::_('JBZOO_JBIMAGE_EDIT_UPLOAD_TEXT_2'); ?></span>
        </span>
    </div>

    <div class="jbimage__from-server clearfix">
        <button type="button" class="jsServerUpload btn btn-small" data-id="<?php echo $this->identifier; ?>"><i
                class="icon-plus"></i> <?php echo Text::_('JBZOO_JBIMAGE_EDIT_UPLOAD_FROM_SERVER'); ?></button>
    </div>

    <div class="jsUploadAlert jbimage__alert" style="display: none;"></div>

    <div class="jsUploadProgress jbimage__progress" style="display: none;">
        <div class="jbimage__progress-bar"></div>
    </div>

    <ul class="jsUploadFiles jbimage__files">
        <?php
        if (!empty($images))
        {
            foreach ($images as $key => $image)
            {
                $image = array(
                    'file'   => isset($image['file']) ? $image['file'] : '',
                    'title'  => isset($image['title']) ? $image['title'] : '',
                    'link'   => isset($image['link']) ? $image['link'] : '',
                    'target' => isset($image['target']) ? $image['target'] : '',
                    'rel'    => isset($image['rel']) ? $image['rel'] : '',
                );

                if ($this->isFileExists($image['file']))
                {
                    $img       = $this->app->zoo->resizeImage($this->app->path->path('root:' . $image['file']), 150, 100);
                    $img       = $this->app->path->relative($img);
                    $imageName = basename($image['file']);

                    $deleteType = $this->config->get('delete_type', 'simple');

                    if (strpos($image['file'], $this->config->get('upload_directory')) === false)
                    {
                        $deleteType = 'simple';
                    } ?>

                    <li class="jbimage__file-item">
                        <div class="jbimage__file-item-body">
                            <div class="jbimage__file-item-teaser">
                                <img src="/<?php echo $img; ?>">
                                <a href="#" data-type="<?php echo $deleteType; ?>"
                                   class="jsUploadDelete jbimage__file-item-delete jbimage__file-item-delete--<?php echo $deleteType; ?>"></a>
                            </div>

                            <p>
                                <i class="jbimage__file-item-icon jbimage__file-item-icon--check"></i> <?php echo $imageName; ?>
                            </p>

                            <div class="jbimage__file-item-edit">
                                <button type="button" class="btn btn-small" data-toggle="collapse"
                                        data-target="#<?php echo $this->identifier . '-edit-' . $key; ?>">
                                    <span></span>
                                </button>

                                <div id="<?php echo $this->identifier . '-edit-' . $key; ?>" class="collapse out">
                                    <div>
                                        <?php echo $this->app->html->_('control.text', $this->getControlName('title', $key), $image['title'], ' size="60" maxlength="255" title="' . Text::_('Title') . '" placeholder="' . Text::_('Title') . '"'); ?>
                                    </div>
                                    <div>
                                        <?php echo $this->app->html->_('control.text', $this->getControlName('link', $key), $image['link'], ' size="60" maxlength="255" title="' . Text::_('Link') . '" placeholder="' . Text::_('Link') . '"'); ?>
                                    </div>
                                    <div>
                                        <?php echo $this->app->html->_('control.text', $this->getControlName('rel', $key), $image['rel'], ' size="60" maxlength="255" title="' . Text::_('Rel') . '" placeholder="' . Text::_('Rel') . '"'); ?>
                                    </div>
                                    <div>
                                        <?php echo Text::_('New window'); ?><?php echo $this->app->html->_('select.booleanlist', $this->getControlName('target', $key), $image['target'], $image['target']); ?>
                                    </div>
                                </div>
                            </div>

                            <input style="display: none;" name="<?php echo $this->getControlName('file', $key); ?>"
                                   type="text" value="<?php echo $image['file']; ?>">
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
