/**
 * JBZoo App is universal Joomla CCK, application for YooTheme Zoo component
 *
 * @package     jbzoo
 * @version     2.x Pro
 * @author      JBZoo App http://jbzoo.com
 * @copyright   Copyright (C) JBZoo.com,  All rights reserved.
 * @license     http://jbzoo.com/license-pro.php JBZoo Licence
 */

;
(function ($, window, document, undefined) {

    /**
     * JBZoo Progress bar
     * @param options
     * @constructor
     */
    $.fn.JBZooProgressBar = function (options) {

        function timeFormat(seconds) {

            if (seconds <= 0 || isNaN(seconds)) {
                return '00:00';
            }

            var formatedMin = Math.floor(seconds / 60),
                formatedSec = seconds % 60;

            if (formatedSec < 10) {
                formatedSec = '0' + formatedSec;
            }

            if (formatedMin < 10) {
                formatedMin = '0' + formatedMin;
            }

            return formatedMin + ':' + formatedSec;
        }

        var options = $.extend({}, {
            'text_complete'     : "Complete!",
            'text_stop_confirm' : "Are you sure?",
            'text_start_confirm': "Are you sure?",
            'text_start'        : "Start",
            'text_stop'         : "Stop",
            'text_ready'        : "Ready",
            'text_wait'         : "Wait please ...",
            'autoStart'         : false,
            'url'               : '',
            'onStart'           : $.noop,
            'onStop'            : $.noop,
            'onRequest'         : $.noop,
            'onTimer'           : $.noop,
            'onFinal'           : function (callback) {
                callback()
            }
        }, options);

        // init html
        var $obj = $(this);
        $obj.html('<div id="jbprogressbar" class="uk-progress">' +
            '<div class="uk-progress-bar" style="width: 100%;">' + options.text_ready + '</div>' +
            '</div>' +
            '<div class="clr"></div>' +
            '<input type="submit" class="jsStart uk-button uk-button-success" value="' + options.text_start + '" />' +
            '<input type="button" class="jsStop uk-button" value="' + options.text_stop + '" style="display:none;" />'
        );

        // vars
        var $bar = $('#jbprogressbar', $obj),
            $progress = $('.uk-progress-bar', $obj),
            $label = $(".progress-label", $obj),
            $start = $('.jsStart', $obj),
            $stop = $('.jsStop', $obj),
            currentProgress = 0,
            secondsPassed = 0,
            stopFlag = true,
            timerId = 0,
            page = 0;

        function timerStart() {
            secondsPassed = 0;
            timerId = setInterval(function () {
                options.onTimer({
                    'passed'   : timeFormat(++secondsPassed),
                    'remaining': timeFormat(parseInt((secondsPassed * 100 / currentProgress) - secondsPassed, 10))
                });
            }, 1000);
        }

        function timerStop() {
            clearInterval(timerId);
        }

        function triggerStart() {
            currentProgress = 0;
            $start.hide();
            $stop.show();
            $bar.addClass('uk-progress-striped uk-active');
            $('.jsErrorBlockWrapper').hide();
            $('.jsErrorBlock').empty();

            stopFlag = false;
            page = 0;
            request(0);

            options.onStart();
            timerStart();
        }

        function triggerStop() {
            $start.show();
            $stop.hide();
            $bar.removeClass('uk-progress-striped uk-active');

            stopFlag = true;
            timerStop();
            options.onStop();
        }

        /**
         * Request for step in server
         * @param page
         */
        function request(page) {

            if (stopFlag || currentProgress >= 100) {
                triggerStop();
                return;
            }

            JBZoo.ajax({
                'url'    : options.url,
                'data'   : {
                    'page': page
                },
                'success': function (data, status) {
                    currentProgress = data.progress;
                    options.onRequest(data);
                    $progress.css('width', currentProgress + '%');

                    if (data.progress >= 100) {

                        $progress.text(options.text_wait);
                        options.onFinal(function () {
                            $progress.text(options.text_complete);
                        });

                        triggerStop();

                    } else {
                        $progress.text(currentProgress + ' %');
                        request(++page);
                    }
                },
                'onFatal': function (responce) {

                    if (!JBZoo.empty(responce.responseText)) {
                        $('.jsErrorBlock').html(responce.responseText);

                    } else if (!JBZoo.empty(responce[0].responseText)) {
                        $('.jsErrorBlock').html(responce[0].responseText);
                    }

                    $('.jsErrorBlockWrapper').fadeIn();
                    triggerStop();
                }
            });
        }

        $start.bind('click', function () {
            JBZoo.confirm(options.text_start_confirm, function () {
                triggerStart();
            });
            return false;
        });

        $stop.bind('click', function () {
            JBZoo.confirm(options.text_stop_confirm, function () {
                triggerStop();
            });
            return false;
        });

        // autostart init
        if (options.autoStart) {
            triggerStart();
            $start.hide();
            $stop.hide();
        }

    };

})(jQuery, window, document);