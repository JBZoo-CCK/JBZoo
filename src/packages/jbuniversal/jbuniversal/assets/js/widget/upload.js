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
 */

;
(function ($, window, document, undefined) {

    JBZoo.widget('JBZoo.Upload', {
            'url' : '',
            'id' : '',
            'upload' : '',
            'controlName' : 'file',
            'index' : 0,
            'repeatable' : false,
            'maxNumberOfFiles' : '',
            'previewMaxWidth' : '',
            'previewMaxHeight' : '',
            'maxFileSize' : '',
            'imageMaxWidth' : '',
            'imageMaxHeight': '',
            'deleteType' : 'simple',
            'paramName' : '',
            'class' : 'upload',
            'types' : 'gif|jpe?g|png',
            'watermark' : '',
            'trusted_mode' : 0,
            'position' : 'top-right'
        }, {
            $input : false,
            $initAdd : false,
            $initDelete : false,

            init: function ($this) {
                // get links to DOM
                $this.$input        = $this.$('.jsUploadInput');
                $this.$dropZone     = $this.$('.jsUploadZone');
                $this.$files        = $this.$('.jsUploadFiles');
                $this.$alert        = $this.$('.jsUploadAlert');
                $this.$progress     = $this.$('.jsUploadProgress');

                $this._initUploads();
                $this._initServerUploads();
                $this._editTitle();

                if ($this.options.maxNumberOfFiles > 1) {
                    $this._initSortable(this.el);
                }

                // Delete file

                $(document).on('click', '.jsUploadDelete', function(e) {
                    e.preventDefault();

                    var button  = $(this);
                    var type    = button.data('type');

                    if (type !== 'simple') {
                        var url = button.data('url');

                        button.closest('.jsUpload').JBZooUpload('delete', url);
                    }

                    $(this).closest('li').remove();

                    $this._updateFilesCount();

                    return false;
                });

                // New instance on add button click for repeatable element

                if ($this.options.repeatable) {
                    var repeatableContainer = $this.el.closest('.repeatable-list');

                    if (!repeatableContainer.data('initAdd')) {

                        $(document).on('click', '#' + $this.options.id +  ' p.add', function() {
                            setTimeout(function () {
                                $this.options.index = repeatableContainer.find('> li:not(.hidden)').length - 1;

                                var addedItem = repeatableContainer.find('.repeatable-element').last();

                                // Init upload
                                addedItem.find('.jsUpload').JBZooUpload($this.options);

                                // Init sortable
                                if ($this.options.maxNumberOfFiles > 1) {
                                    $this._initSortable(addedItem);
                                }

                            }, 300);
                        });

                    }

                    repeatableContainer.data('initAdd', true);
                }
            },

            _initUploads: function () {

                var $this = this;

                $this.$input.fileupload({
                    dropZone: $this.$dropZone,
                    url: $this.options.url,
                    dataType: 'json',
                    autoUpload: true,
                    acceptFileTypes: new RegExp('(\.|\/)(' + $this.options.types + ')', 'i'),
                    maxFileSize: $this.options.maxFileSize,
                    loadImageMaxFileSize: $this.options.maxFileSize,
                    maxChunkSize: $this.options.maxFileSize,
                    maxNumberOfFiles: $this.options.maxNumberOfFiles,
                    getNumberOfFiles: function () {
                        return $this.$files.find('> *').length - 1;
                    },
                    messages: {
                        maxNumberOfFiles: JBZoo.getVar('JBZOO_UPLOAD_ERROR_12'),
                        acceptFileTypes: JBZoo.getVar('JBZOO_UPLOAD_ERROR_11'),
                        maxFileSize: JBZoo.getVar('JBZOO_UPLOAD_ERROR_9'),
                        minFileSize: JBZoo.getVar('JBZOO_UPLOAD_ERROR_10'),
                    },
                    disableImageResize: /Android(?!.*Chrome)|Opera/
                        .test(window.navigator && navigator.userAgent),
                    previewMaxWidth: $this.options.previewMaxWidth,
                    previewMaxHeight: $this.options.previewMaxHeight,
                    previewCrop: true,
                    imageMaxWidth: $this.options.imageMaxWidth,
                    imageMaxHeight: $this.options.imageMaxHeight,
                    formData: {
                        upload: $this.options.upload,
                        paramName: $this.options.paramName,
                        accept: $this.options.types,
                        watermark: $this.options.watermark,
                        position: $this.options.position
                    }
                }).on('fileuploadadd', function (e, data) {
                    $this.$files.show();

                    data.context = $('<li class="' + $this.options.class + '__file-item"/>').appendTo($this.$files);
                    $.each(data.files, function (index, file) {
                        var node = $('<div class="' + $this.options.class + '__file-item-body"/>')
                                .append($('<p/>').html('<i class="' + $this.options.class + '__file-item-icon ' + $this.options.class + '__file-item-icon--spin"></i> ' + file.name));
                        node.appendTo(data.context);
                    });
                }).on('fileuploadprocessalways', function (e, data) {
                    var index = data.index,
                        file = data.files[index],
                        node = $(data.context.children()[index]);;

                    if (file.preview) {
                        var newFile = data.files[0].preview;

                        if (newFile.localName === 'canvas')
                        {
                            var newFile = $('<' + $this.options.tag + '/>').attr('src', newFile.toDataURL()).css({"max-width": "100%", "max-height": $this.options.previewMaxHeight + "px"});
                        }

                        var fileWrap = $('<div class="' + $this.options.class + '__file-item-teaser"/>');
                        fileWrap     = fileWrap.prepend(newFile);

                        node.prepend(fileWrap);
                    }

                    if (file.error) {
                        node.closest('li').remove();

                        $this.$alert.show().append('<p class="">' + JBZoo.getVar('JBZOO_UPLOAD_ERROR_FILE') + ' <b>' + file.name + '</b> ' + JBZoo.getVar('JBZOO_UPLOAD_ERROR_REASON') + ' ' + file.error + '.</p>');
                        node.closest('.' + $this.options.class + '__images').remove();
                    }
                }).on('fileuploadprogressall', function (e, data) {
                    var progress = parseInt(data.loaded / data.total * 100, 10);

                    $this.$progress.show().find('.' + $this.options.class + '__progress-bar').css(
                        'width',
                        progress + '%'
                    );

                    if (progress == 100) {
                        setInterval(function(container) {
                            $this.$progress.hide();
                        }, 2000, $(this));
                    };

                }).on('fileuploaddone', function (e, data) {
                    $.each(data.result[$this.options.paramName], function (index, file) {
                        if (file.url) {
                            var url             = file.url;
                            url                 = url.replace(location.protocol + '//' + location.host + '/', '').replace('//', '/');
                            var count           = parseInt($this.el.data('count'));
                            var extraSettings   = $this._getExtraSettings(count);

                            $(data.context.children()[index])
                                .closest('.' + $this.options.class + '__file-item')
                                .addClass($this.options.class + '__file-item_success')
                                .find('.' + $this.options.class + '__file-item-icon').removeClass($this.options.class + '__file-item-icon--spin')
                                .addClass($this.options.class + '__file-item-icon--check')
                                .closest('.' + $this.options.class + '__file-item').find('.' + $this.options.class + '__file-item-body').append(extraSettings)
                                .closest('.' + $this.options.class + '__file-item').find('.' + $this.options.class + '__file-item-teaser').append('<a href="#" data-type="' + $this.options.deleteType + '" href="#" data-url="' + file.deleteUrl + '" class="jsUploadDelete ' + $this.options.class + '__file-item-delete ' + $this.options.class + '__file-item-delete--' + $this.options.deleteType + '"></a>')
                                .append('<input style="display: none" name="elements[' + $this.options.id + ']' + ($this.options.repeatable ? '[' + $this.options.index + '][' + $this.options.controlName + ']' : '') + '[' + count + '][file]" type="text" value="' + url + '">');

                            count++;
                            $this.el.data('count', count);

                            $this.$alert.html('').hide();

                        } else if (file.error) {
                            $this.$alert.show().append('<p class="">' + JBZoo.getVar('JBZOO_UPLOAD_ERROR_FILE') + ' <b>' + file.name + '</b> ' + JBZoo.getVar('JBZOO_UPLOAD_ERROR_REASON') + ' ' + file.error + '.</p>');
                        }
                    });
                }).on('fileuploadfail', function (e, data) {
                    $.each(data.files, function (index) {
                        $this.$alert.show().append('<p class="">' + JBZoo.getVar('JBZOO_UPLOAD_ERROR_FILE') + ' <b>' + file.name + '</b> ' + JBZoo.getVar('JBZOO_UPLOAD_ERROR_REASON') + ' ' + file.error + '.</p>');
                    });
                });
            },

            /**
             * Init Sortable
             */

            _initSortable : function(container) {
                $this = this;

                var sortable = new Sortable(container.find('.jsUploadFiles').get(0), {
                    sort: true,  // sorting inside list
                    delay: 0, // time in milliseconds to define when the sorting should start
                    animation: 150,  // ms, animation speed moving items when sorting, `0` — without animation
                    easing: "cubic-bezier(1, 0, 0, 1)", // Easing for animation. Defaults to null. See https://easings.net/ for examples.
                });
            },

            /**
             * Init Server Upload
             */

            _initServerUploads : function() {
                $this = this;

                // For site area
                if (!location.href.match(/^(.+)administrator\/index\.php.*/i))
                {
                    return;
                }

                var url = location.href.match(/^(.+)administrator\/index\.php.*/i)[1];
                var elemId = 'jbupload--' + $this.options.class + '--' + $this.options.id,

                $selectButton = $this.$('.jsServerUpload');
                
                

                $selectButton.click(function (event) {
                    event.preventDefault();
                    
                    var e = $(".jsUploadInput");
                    //alert('Извините, не работает, нужен JS специалист для правки SqueezeBox, его нужно подружить с Joomla.initialiseModal. Рабочий пример с медиа менеджером есть в стандартном элементе Zoo image (элемент Изображения). t.me/jbzoo');
                    Joomla.initialiseModal ? $this.r(e) : SqueezeBox.fromElement(this, {
                    //SqueezeBox.fromElement(this, {
                        handler: "iframe",
                        url    : "index.php?option=com_media&view=images&tmpl=component&e_name=" + elemId,
                        size   : {x: 850, y: 500}
                    });
                });
                
                if ($.isFunction(window.jInsertEditorText) && !$.isFunction(window.insertTextOldUpload)) {
                    window.insertTextOldUpload = window.jInsertEditorText;
                }
                
                window.jInsertEditorText = function (c, a) { // text, id
                    
                    // TODO Repeat Element server upload

                    if (a.match(new RegExp('jbupload', 'i'))) {
                        var requestArray    = a.split('--');
                        var elemId          = requestArray[2];
                        var container       = $('input[name*="' + elemId + '"]').closest('.jsUpload');
                        var options         = container.data('JBZooUpload').options;
                        var count           = parseInt(container.data('count'));
                        var extraSettings   = $this._getExtraSettings(count, options);
                        var value           = c.match(/src="([^\"]*)"/)[1];
                        var html            = '<li class="' + options.class + '__file-item ' + options.class + '__file-item_success">' +
                            '<div class="' + options.class + '__file-item-body">' +
                                '<div class="' + options.class + '__file-item-teaser">' +
                                    '<' + $this.options.tag + ' src="' + url + value + '">' +
                                    '<a href="#" data-type="simple" class="jsUploadDelete ' + options.class + '__file-item-delete ' + options.class + '__file-item-delete--simple"></a>' +
                                '</div>' +
                                '<p><i class="' + options.class + '__file-item-icon ' + options.class + '__file-item-icon--check"></i> ' + value.substr(value.lastIndexOf("/") + 1) + '</p>' +
                                extraSettings +
                                '<input type="text" name="elements[' + elemId + '][' + count + '][file]" value="' + value + '" style="display:none;">' +
                            '</div>' +
                        '</li>';

                        count++;

                        container.data('count', count).find('.jsUploadFiles').append(html);

                    } else {
                        $.isFunction(window.insertTextOldUpload) && window.insertTextOldUpload(c, a);
                    }
                };
            },

            /**
             * Delete file
             */

            delete : function(url) {
                $this = this;

                $.ajax({
                    type: 'GET',
                    url: '?option=com_zoo&controller=elements&task=token',
                    success: function(data) {
                        var token       = data.result;
                        var deleteUrl   = url + '&' + token + '=1';

                        $.ajax({
                            type: 'POST',
                            url: deleteUrl,
                            data: {
                                upload: $this.options.upload,
                                paramName : $this.options.paramName
                            },
                            success: function(data){
                                $this._updateFilesCount();
                            }
                        });
                    }
                });

                return;
            },

            /**
             * Update file count
             */

            _updateFilesCount : function() {
                var $this = this;
                var filesCount = $this.$files.find('> *').length;

                if (filesCount == 0) {
                   $this.$progress.hide();
                   $this.$files.hide();
                } else {
                    $this.$files.show();
                }

                $this.$alert.html('').hide();
            },

            /**
             * Get Extra Settings
             */

            _getExtraSettings : function(count, options) {
                var $this           = this;
                var extraSettings   = '';
                var options         = options || $this.options;

                if (options.trusted_mode) {
                    extraSettings = '<div class="' + options.class + '__file-item-edit">' +
                        '<button type="button" class="btn btn-small" data-toggle="collapse" data-target="#' + options.id + '-edit-' + count + '"><span></span></button>' +
                        '<div id="' + options.id + '-edit-' + count + '" class="collapse out">' +
                            '<div>' +
                                '<input type="text" name="elements[' + options.id + '][' + count + '][title]" value="" maxlength="255" placeholder="' + JBZoo.getVar('JBZOO_UPLOAD_EXTRA_OPTIONS_TITLE') + '">' +
                            '</div>' +
                            '<div>' +
                                '<input type="text" name="elements[' + options.id + '][' + count + '][link]" value="" maxlength="255" placeholder="' + JBZoo.getVar('JBZOO_UPLOAD_EXTRA_OPTIONS_LINK') + '">' +
                            '</div>' +
                            '<div>' +
                                '<input type="text" name="elements[' + options.id + '][' + count + '][rel]" value="" maxlength="255" placeholder="' + JBZoo.getVar('JBZOO_UPLOAD_EXTRA_OPTIONS_REL') + '">' +
                            '</div>' +
                            '<div>' + JBZoo.getVar('JBZOO_UPLOAD_EXTRA_OPTIONS_TARGET') + ' ' +
                                '<div class="controls">' +
                                    '<label for="elements[' + options.id + '][' + count + '][target]0" id="elements[' + options.id + '][' + count + '][target]0-lbl" class="radio">' +
                                        '<input type="radio" name="elements[' + options.id + '][' + count + '][target]" id="elements[' + options.uploadId + '][' + count + '][target]0" value="0" checked="checked" 0="">' + JBZoo.getVar('JBZOO_UPLOAD_EXTRA_OPTIONS_TARGET_NO') +
                                    '</label>' +
                                    '<label for="elements[' + options.id + '][' + count + '][target]1" id="elements[' + options.id + '][' + count + '][target]1-lbl" class="radio">' +
                                        '<input type="radio" name="elements[' + options.id + '][' + count + '][target]" id="elements[' + options.id + '][' + count + '][target]1" value="1" 0="">' + JBZoo.getVar('JBZOO_UPLOAD_EXTRA_OPTIONS_TARGET_YES') +
                                    '</label>' +
                                '</div>' +
                            '</div>' +
                        '</div>' +
                    '</div>';
                }

                return extraSettings;
            },
            
            r: function (t) {
                    
                    var e = "index.php?option=com_media&amp;view=media&amp;tmpl=component&amp;mediatypes=0&amp;asset=com_content&amp;path=",
                        a = $(`<div tabindex="-1" class="joomla-modal modal fade" aria-modal="true" role="dialog">
                        <div class="modal-dialog modal-lg jviewport-width80">
                        <div class="modal-content">
                        <div class="modal-header">
                        <h3 class="modal-title">Choose Image</h3>
                        </div>
                        <div class="modal-body jviewport-height60"><iframe class="iframe" src="` + e + `" name="Change Image" height="100%" width="100%"></iframe>
                        </div>
                        <div class="modal-footer">
                        <button type="button" class="btn btn-success button-save-selected">Select</button><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button></div>
                        </div>
                        </div>
                        </div>`).insertBefore(t)[0];
                        
                    Joomla.initialiseModal(a, {
                        isJoomla: !0
                    }), 
                    a.querySelector(".button-save-selected").addEventListener("click", function() {
                        
                        Joomla.getMedia(Joomla.selectedMediaFile, 
                        t[0], 
                        {
                            updatePreview: function() {},
                            markValid: function() {},
                            setValue: function(i) {
                                var elemId = 'jbupload--' + $this.options.class + '--' + $this.options.id
                                console.log('3------', elemId);
                                //t.val(i)
                                console.log(i.replace(/#.*/, ""));
                                //return i.replace(/#.*/, "");
                                
                               
                                
                                var a = elemId;
                                if (a.match(new RegExp('jbupload', 'i'))) {
                                    var url             = '/' + i.replace(/#.*/, "");
                                    
                                    
                                    var requestArray    = a.split('--');
                                    var elemId          = requestArray[2];
                                    var container       = $('input[name*="' + elemId + '"]').closest('.jsUpload');
                                    var options         = container.data('JBZooUpload').options;
                                    var count           = parseInt(container.data('count'));
                                    var extraSettings   = $this._getExtraSettings(count, options);
                                    //var value           = i.match(/src="([^\"]*)"/)[1];
                                    var value           = '/' + i.replace(/#.*/, "");
                                    var html            = '<li class="' + options.class + '__file-item ' + options.class + '__file-item_success">' +
                                        '<div class="' + options.class + '__file-item-body">' +
                                            '<div class="' + options.class + '__file-item-teaser">' +
                                                '<' + $this.options.tag + ' src="' + url /*+ value*/ + '">' +
                                                '<a href="#" data-type="simple" class="jsUploadDelete ' + options.class + '__file-item-delete ' + options.class + '__file-item-delete--simple"></a>' +
                                            '</div>' +
                                            '<p><i class="' + options.class + '__file-item-icon ' + options.class + '__file-item-icon--check"></i> ' + value.substr(value.lastIndexOf("/") + 1) + '</p>' +
                                            extraSettings +
                                            '<input type="text" name="elements[' + elemId + '][' + count + '][file]" value="' + value + '" style="display:none;">' +
                                        '</div>' +
                                    '</li>';
            
                                    count++;
            
                                    container.data('count', count).find('.jsUploadFiles').append(html);
            
                                }
        
                            
                                        
                                
                                
                                
                                
                                
                                
                                
                                
                            }
                        }).then(function() {
                            //t.val(t.val().replace(/#.*/, "")), n(t), 
                            a.close()
                        })
                    }), 
                    a.addEventListener("hidden.bs.modal", function(i) {
                        $(a).remove()
                    }), 
                    Joomla.selectedMediaFile = {}, a.open()
                    
                },
                
                _editTitle : function() {
                    jQuery('.jbimage__file-item-edit button').click(function (event) {
                            event.preventDefault();
                            
                            var elem = this.parentNode.querySelector('.collapse');
                            
                            if(elem.className.split(" ").indexOf("show") >= 0) {
                                elem.classList.remove("show");
                            } else {
                                elem.classList.add("show");
                            }
                            
                            
                    });
                }
            
            
        }
        
        
        
        
        
        
        
    );
    
    

})(jQuery, window, document);
