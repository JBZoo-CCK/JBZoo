(function($){

    var methods = {
        init : function(options) {

            var options = $.extend({
                'uploadId': '',
                'url' : '',
                'previewMaxWidth' : 175,
                'previewMaxHeight' : 110,
                'maxFileSize' : '',
                'imageMaxWidth' : '',
                'imageMaxHeight': '',
                'userId' : 'quest'
            }, options);

            var $this = $(this);
            var container = $this.closest('.jsContainer');
            
            var fileupload = function() {
                $this.fileupload({
                    dropZone: container.find('.galleryimage-dropzone'),
                    url: options.url + 'index.php',
                    dataType: 'json',
                    autoUpload: true,
                    acceptFileTypes: /(\.|\/)(gif|jpe?g|png)$/i,
                    disableImageResize: /Android(?!.*Chrome)|Opera/
                        .test(window.navigator && navigator.userAgent),
                    previewMaxWidth: options.previewMaxWidth,
                    previewMaxHeight: options.previewMaxHeight,
                    previewCrop: true,
                    imageMaxWidth: options.imageMaxWidth,
                    imageMaxHeight: options.imageMaxHeight,
                    sequentialUploads: true,
                    formData: {id: options.userId, elementId: options.uploadId}
                }).on('fileuploadadd', function (e, data) {
                    data.context = $('<li class="fl-sort-images well well-small text-left"/>').appendTo(container.find('.fl-files'));
                    $.each(data.files, function (index, file) {
                        var node = $('<div class="media"/>')
                                .append($('<div class="media-body"/>')
                                .append($('<h3 class="media-heading"/>').html('<i class="fluploader-load-icon"></i> ' + file.name)));
                        node.appendTo(data.context);
                    });
                }).on('fileuploadprocessalways', function (e, data) {
                    var index = data.index,
                        file = data.files[index],
                        node = $(data.context.children()[index]);

                    if (file.preview) {
                        var canvas = data.files[0].preview;
                        var newImg = $('<img class="media-object img-rounded"/>').attr('src', canvas.toDataURL()).css({"max-width": options.previewMaxWidth + "px", "max-height": options.previewMaxHeight + "px"});
                        var imgWrap = $('<div class="pull-left"/>');
                        imgWrap = imgWrap.prepend(newImg);
                        node.prepend(imgWrap);
                    }
                    if (file.error) {
                        container.find('.fl-alert').removeClass('hidden').append('<p style="font-size:smaller; word-wrap:break-word; margin-bottom: 0;">Файл <b>' + file.name + '</b> не загружен. Причина: ' + file.error + '.</p>');
                        node.closest('.fl-sort-images').remove();
                    }
                }).on('fileuploadprogressall', function (e, data) {
                    var progress = parseInt(data.loaded / data.total * 100, 10);

                    container.find('.progress').removeClass('hidden').find('.bar').css(
                        'width',
                        progress + '%'
                    );

                    if (progress == 100) {
                        $('#fluploadertextarea').val('');
                        $('.jsFLUploaderAdd img').remove();
                    }
                    
                }).on('fileuploaddone', function (e, data) {
                    var flgalleryimageSubmission = container;
                    var textareaValue = $('.jsFLUploaderAdd').val();
                    $.each(data.result[options.uploadId + '-files'], function (index, file) {
                        if (file.url) {

                            var url = file.url;
                            url = url.replace(location.protocol + '//' + location.host + '/', '').replace('//', '/');

                            var count = container.find('.fl-files li').length;
    
                            var imagesCount = parseInt(flgalleryimageSubmission.attr('fl-galleryimage-images'));

                            var link = $('<div class="fl-galleryimage-thumb text-success text-left media"/>');

                            $(data.context.children()[index])
                                .wrap(link)
                                .find('.media-body')
                                .append('<div class="input-append"><input type="text" name="fluploader-path" value="' + file.url + '" disabled><button class="btn jsCopyBtn" type="button" data-clipboard-text="' + file.url + '"><img class="clippy" src="/media/zoo/applications/jbuniversal/elements/jbuploader/assets/img/clippy.svg" alt="Скопировать" width="13"></button></div>')
                                .find('.fluploader-load-icon').removeClass('icon-refresh')
                                .addClass('flicon-ok');

                            imagesCount++;
                            flgalleryimageSubmission.attr('fl-galleryimage-images', imagesCount);

                            var copyBtn = $(data.context.children()[index]).find('button.jsCopyBtn');

                            copyBtn.tooltip({
                                trigger: 'click',
                                placement: 'bottom'
                            });

                            var clipboard = new Clipboard(data.context.children()[index].querySelector(' button.jsCopyBtn'));
                            clipboard.on('success', function(e) {
                                methods.setTooltip(e.trigger, 'Ссылка скопирована!');
                                methods.hideTooltip(e.trigger);
                            });

                            clipboard.on('error', function(e) {
                                methods.setTooltip(e.trigger, 'Копирование не удалось :-(');
                                methods.hideTooltip(e.trigger);
                            });

                        } else if (file.error) {
                            container.find('.fl-alert').removeClass('uk-hidden').append('<p style="font-size:smaller; word-wrap:break-word; margin-bottom: 0;">Файл <b>' + file.name + '</b> не загружен. Причина: ' + file.error + '.</p>');
                        }
                    });
                }).on('fileuploadfail', function (e, data) {
                    $.each(data.files, function (index) {
                        container.find('.fl-alert').removeClass('hidden').append('<p style="font-size:smaller; word-wrap:break-word; margin-bottom: 0;">Файл <b>#' + index + '</b> не загружен.</p>');
                    });
                }).prop('disabled', !$.support.fileInput).parent().addClass($.support.fileInput ? undefined : 'disabled');
            }


            $('.jsFLUploaderAdd').on('click', function(event) {
                event.preventDefault();

                var btnUpload = $(this);

                
            });

            return this.each(fileupload);
        },

        setTooltip: function (btn, message) {
            $(btn).attr('data-original-title', message)
                .tooltip('show');
        },

        hideTooltip: function (btn) {
            setTimeout(function() {
                $(btn).tooltip('hide');
            }, 2000);
        },

        addImages: function(server) {
            input = $(this);
            btnUpload = input.closest('.jsContainer').find('.jsFLUploaderAdd');

            btnUpload.prepend('<img src="/media/zoo/applications/jbuniversal/elements/jbuploader/assets/img/loader.svg" alt="Скопировать" width="13">');

            var urlValues = $('#fluploadertextarea').val();
            var lines = urlValues.split('\n');

            for(var i = 0; i < lines.length; i++){
                var url = lines[i];
                if (url) {
                    $.getImageData({
                        url: url,
                        server: server + 'getimage.php',
                        success: function (img, type, name) {

                            var canvas = document.createElement('canvas');
                            canvas.width = img.width;
                            canvas.height = img.height;
                            if (canvas.getContext && canvas.toBlob) {
                                canvas.getContext('2d').drawImage(img, 0, 0, img.width, img.height);
                                canvas.toBlob(function (blob) {
                                    blob.name = name;
                                    setTimeout(function() {
                                        input.fileupload('add', {files: [blob]});
                                    }, 500);
                                }, type);
                            }
                        }, 
                        error: function (data, status) {
                            btnUpload.find('img').remove();

                            $('#fluploadertextarea').val(urlValues.replace(url, ''));

                            input.closest('.jsContainer').find('.fl-alert').removeClass('hidden').append('<p style="font-size:smaller; word-wrap:break-word; margin-bottom: 0;">Ошибка при загрузке URL <b>' + url + '</b>. URL был удален из списка.</p>');
                        }
                    });
                }
            }
        }
    };

    $.fn.FLUploaderEdit = function(method) {
        
        if ( methods[method] ) {
            return methods[ method ].apply( this, Array.prototype.slice.call( arguments, 1 ));
        } else if ( typeof method === 'object' || ! method ) {
            return methods.init.apply( this, arguments );
        } else {
            $.error('Метод с именем ' +  method + ' не существует для FLUploaderEdit`');
        } 
    };

})(jQuery);