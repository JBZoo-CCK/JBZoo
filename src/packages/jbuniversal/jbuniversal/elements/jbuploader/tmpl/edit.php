<?php
/**
 * @package   FL Gallery Image Element for Zoo
 * @author    Дмитрий Васюков http://fictionlabs.ru
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

$params = $this->config;
$user = JFactory::getUser();
$userId = ($this->getItem()->created_by) ? $this->getItem()->created_by : $user->id;

?>
<div class="row <?php echo $this->identifier; ?> fl-galleryimage-submission jsContainer">
    <div class="fl-galleryimage uk-grid">
        <div class="uk-width-1-1">
            <div class="row">
                <div class="span6">
                    <div class="upload">          
                        <div id="fl-galleryimage-dropzone" class="galleryimage-dropzone well text-center">
                            <i class="icon-upload text-large"></i> Перетащите изображения сюда или <div class="fl-file text-error">выберите<input class="<?php echo $this->identifier; ?>-galleryimage-fileupload" type="file" name="<?php echo $this->identifier; ?>-files[]" multiple></div>
                        </div>
                    </div>
                </div>
                <div class="span6 text-right">
                    <textarea name="fluploader" id="fluploadertextarea" placeholder="Ссылки на изображения с других сайтов"></textarea>
                    <a href="#" class="btn btn-small jsFLUploaderAdd btn-fluploaderadd">Загрузить все изображения</a>
                </div>
            </div>

            <div class="progress hidden">
                <div class="bar"></div>
            </div>  

            <div class="fl-alert alert alert-danger hidden"><span title="Спрятать предупреждение" class="alert-close close"></span></div>
            
            <ul class="fl-files media-list">
            </ul>   
        </div>
    </div>
</div>

<script>
    (function($){
        $('.<?php echo $this->identifier; ?>-galleryimage-fileupload').FLUploaderEdit({
            'uploadId': '<?php echo $this->identifier; ?>',
            'url' : '<?php echo JURI::root(); ?>media/zoo/applications/jbuniversal/elements/jbuploader/upload/',
            'maxNumberOfFiles' : <?php echo $params->get('max_number') ? $params->get('max_number') : 100; ?>,
            'previewMaxWidth' : <?php echo $params->get('thumb_width') ? $params->get('thumb_width') : 175; ?>,
            'previewMaxHeight' : <?php echo $params->get('thumb_height') ? $params->get('thumb_height') : 110; ?>,
            'maxFileSize' : <?php echo $params->get('max_upload_size') ? $params->get('max_upload_size')*1000 : 10000000 ?>,
            'imageMaxWidth' : <?php echo $params->get('max_width') ? $params->get('max_width') : 5000000; ?>,
            'imageMaxHeight': <?php echo $params->get('max_height')? $params->get('max_height') : 5000000; ?>,
            'userId' : '<?php echo $userId; ?>',
        }); 

        $(document).on('click', '.<?php echo $this->identifier; ?> .alert .close', function () {
            $(this).parent().addClass('hidden').find('p').remove();
        })

        $('.jsFLUploaderAdd').on('click', function(event) {
            event.preventDefault();
            $('.<?php echo $this->identifier; ?>-galleryimage-fileupload').FLUploaderEdit('addImages', '<?php echo JURI::root(); ?>media/zoo/applications/jbuniversal/elements/jbuploader/upload/');
        });

    })(jQuery);
</script>